#!/usr/bin/env bash
#
# Rapidez installer, try Rapidez with a demo Magento installation
#
#   curl -fsSL https://rapidez.io/install | bash
#
# Everything runs in Docker, that's the only requirement.
# Optional environment variables:
#   RAPIDEZ_HOME     The directory to install into (default: rapidez)
#   RAPIDEZ_VERSION  The rapidez/rapidez version (default: ^5.0)

set -Eeuo pipefail
trap 'echo "" >&2; echo "❌ Something went wrong, check the errors above and try again" >&2' ERR

# Vars
INSTALL_DIR="${RAPIDEZ_HOME:-rapidez}"
RAPIDEZ_VERSION="${RAPIDEZ_VERSION:-^5.0}"
DEFAULT_TRAEFIK_PORT=80
DEFAULT_DB_PORT=3306
CONTAINERS=(traefik elasticsearch magento nginx rapidez)
USER_ID="$(id -u)"
GROUP_ID="$(id -g)"

# Helpers
banner() {
  echo '====================================='
  echo '  ____             _     _          '
  echo ' |  _ \ __ _ _ __ (_) __| | ___ ____'
  echo ' | |_) / _` | '\''_ \| |/ _` |/ _ \_  /'
  echo ' |  _ < (_| | |_) | | (_| |  __// / '
  echo ' |_| \_\__,_| .__/|_|\__,_|\___/___|'
  echo '            |_|                     '
  echo '  Welcome to the Rapidez installer!'
  echo '====================================='
}

step() {
  echo ""
  echo "→ $1"
}

success() {
  echo "✓ $1"
}

fail() {
  echo "" >&2
  echo "❌ $1" >&2
  shift
  for line in "$@"; do
    echo "   $line" >&2
  done
  exit 1
}

command_exists() {
  command -v "$1" >/dev/null 2>&1
}

port_in_use() {
  (echo >"/dev/tcp/127.0.0.1/$1") >/dev/null 2>&1
}

find_free_port() {
  local port=$1

  while port_in_use "$port"; do
    port=$((port + 1))
  done

  echo "$port"
}

set_env() {
  if grep -q "^$1=" .env; then
    sed -i.bak "s|^$1=.*|$1=$2|" .env
    rm -f .env.bak
  else
    echo "$1=$2" >>.env
  fi
}

# Run a command in the Rapidez container as the current user,
# so all files stay owned by you and are editable locally.
in_rapidez() {
  docker compose exec -T --user "$USER_ID:$GROUP_ID" --env HOME=/tmp rapidez "$@"
}

wait_for() {
  local name=$1 timeout=$2 start=$SECONDS
  shift 2

  echo "  Waiting for $name..."
  until "$@" >/dev/null 2>&1; do
    if ((SECONDS - start > timeout)); then
      fail "$name is not ready after $((timeout / 60)) minutes" \
        "Check the logs with: cd $PWD && docker compose logs"
    fi
    sleep 5
  done
  success "$name is ready"
}

elasticsearch_ready() {
  docker compose exec -T rapidez sh -c 'curl -sf -u "$ELASTICSEARCH_USER:$ELASTICSEARCH_PASS" http://elasticsearch:9200/_security/user/web'
}

magento_ready() {
  # On the first start the Rapidez module is installed and everything is reindexed.
  ! docker compose exec -T magento pgrep -f 'entrypoints/|bin/magento|magerun2' >/dev/null &&
    docker compose exec -T rapidez curl -sf -H 'Content-Type: application/json' \
      -d '{"query":"{ storeConfig { store_code } }"}' http://magento/graphql | grep -q store_code
}

# Wrapped in a function so nothing runs when the download is interrupted.
main() {
  banner

  step "Checking the requirements"

  if ! command_exists docker; then
    fail "Docker is not installed!" "Please install Docker and try again: https://docs.docker.com/get-docker/"
  fi

  if ! docker compose version >/dev/null 2>&1; then
    fail "Docker Compose is not available!" "Please update Docker and try again"
  fi

  if ! docker info >/dev/null 2>&1; then
    fail "Docker is not running or you don't have permission to use it!" "Please start Docker and try again"
  fi

  success "Docker found"

  for container in "${CONTAINERS[@]}"; do
    if docker container inspect "$container" >/dev/null 2>&1; then
      fail "There is already a Docker container named \"$container\"" \
        "Remove it first with: docker rm -f $container"
    fi
  done

  if [ -e "$INSTALL_DIR" ] && [ -n "$(ls -A "$INSTALL_DIR")" ]; then
    fail "The directory \"$INSTALL_DIR\" already exists and isn't empty" \
      "Remove it or install somewhere else with: curl -fsSL https://rapidez.io/install | RAPIDEZ_HOME=other-directory bash"
  fi

  step "Downloading Rapidez"

  mkdir -p "$INSTALL_DIR"
  cd "$INSTALL_DIR"

  docker run --rm --user "$USER_ID:$GROUP_ID" --env COMPOSER_HOME=/tmp/composer \
    --volume "$PWD:/app" --workdir /app composer:2 \
    create-project --no-install --no-scripts --no-interaction "rapidez/rapidez:$RAPIDEZ_VERSION" .

  if [ ! -f docker-compose-traefik.yml ] || [ ! -f .env.example.traefik ]; then
    fail "The Docker configuration is missing" "Check the errors above and try again"
  fi

  TRAEFIK_PORT=$(find_free_port "$DEFAULT_TRAEFIK_PORT")
  DB_PORT=$(find_free_port "$DEFAULT_DB_PORT")

  cp .env.example.traefik .env
  set_env TRAEFIK_PORT "$TRAEFIK_PORT"
  set_env DB_PORT "$DB_PORT"

  success "Rapidez downloaded and configured"
  echo "  HTTP port:  $TRAEFIK_PORT"
  echo "  MySQL port: $DB_PORT"

  step "Starting the containers, the first time this takes a while"

  docker compose pull --ignore-buildable
  docker compose up --detach --build

  success "Containers started"

  step "Installing the dependencies"

  in_rapidez composer require --no-interaction rapidez/reviews
  success "PHP dependencies installed"

  in_rapidez php artisan key:generate --no-interaction
  in_rapidez php artisan rapidez:install --frontendonly
  in_rapidez php artisan vendor:publish --provider="Rapidez\Core\RapidezServiceProvider"
  in_rapidez php artisan storage:link
  success "Configs, views and translations published"

  in_rapidez pnpm install
  in_rapidez pnpm run prod
  success "Frontend dependencies installed and built"

  # The webserver runs as another user and needs to write here.
  chmod -R a+rwX storage bootstrap/cache

  step "Indexing the products, Magento needs a few minutes to start the first time"

  wait_for "Elasticsearch" 600 elasticsearch_ready
  wait_for "Magento" 1200 magento_ready

  # Used to validate the customer tokens from Magento.
  set_env CRYPT_KEY "$(docker compose exec -T magento php -r '$env = include "app/etc/env.php"; echo $env["crypt"]["key"];')"

  docker compose exec -T rapidez php artisan rapidez:index
  success "Products indexed"

  local port_suffix=""
  if [ "$TRAEFIK_PORT" != "80" ]; then
    port_suffix=":$TRAEFIK_PORT"
  fi

  echo ""
  echo "🎉 Rapidez is ready!"
  echo ""
  echo "Rapidez frontend:"
  echo "http://rapidez.localhost$port_suffix"
  echo ""
  echo "Magento admin (user: exampleuser, password: examplepassword123):"
  echo "http://magento.localhost$port_suffix/admin"
  echo ""
  echo "Stop or start everything from the $INSTALL_DIR directory with:"
  echo "docker compose stop / docker compose start"
  echo ""
  echo "From here:"
  echo "- Have a look and make some changes in the templates: $PWD/resources/views/vendor/rapidez"
  echo "- Check the docs @ https://docs.rapidez.io"
  echo "- Questions? Join Slack @ https://rapidez.io/slack"
}

main "$@"
