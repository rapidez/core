#!/usr/bin/env bash
set -e

echo '====================================='
echo '  ____             _     _          '
echo ' |  _ \ __ _ _ __ (_) __| | ___ ____'
echo ' | |_) / _` | '\''_ \| |/ _` |/ _ \_  /'
echo ' |  _ < (_| | |_) | | (_| |  __// / '
echo ' |_| \_\__,_| .__/|_|\__,_|\___/___|'
echo '            |_|                     '
echo '  Welcome to the Rapidez installer!'
echo '====================================='

# Vars
DEFAULT_TRAEFIK_PORT=80
DEFAULT_DB_PORT=3306
INSTALL_DIR="${RAPIDEZ_HOME:-rapidez}"

# Helpers
command_exists() {
  command -v "$1" >/dev/null 2>&1
}

find_free_port() {
  local port=$1

  while nc -z localhost "$port" >/dev/null 2>&1; do
    port=$((port + 1))
  done

  echo "$port"
}

# Start!
if ! command_exists docker; then
  echo "❌ Docker is not installed!"
  echo "Please install Docker and try again"
  exit 1
fi

if ! docker compose version >/dev/null 2>&1; then
  echo "❌ Docker Compose is not available!"
  echo "Please update Docker and try again"
  exit 1
fi

echo "✓ Docker found"

mkdir -p "$INSTALL_DIR"
cd "$INSTALL_DIR"

composer create-project rapidez/rapidez:^5.0 .
composer require rapidez/reviews

if [ ! -f docker-compose.yml ]; then
  echo "⚠️  No docker-compose.yml found"
  echo "Check the errors and try again"
  exit 1
fi

TRAEFIK_PORT=$(find_free_port "$DEFAULT_TRAEFIK_PORT")
DB_PORT=$(find_free_port "$DEFAULT_DB_PORT")

echo "✓ Free ports found:"
echo "  HTTP:  $TRAEFIK_PORT"
echo "  MySQL: $DB_PORT"

cp .env.example.traefik .env
sed -i.bak "s/^TRAEFIK_PORT=.*/TRAEFIK_PORT=$TRAEFIK_PORT/" .env && rm -f .env.bak
sed -i.bak "s/^DB_PORT=.*/DB_PORT=$DB_PORT/" .env && rm -f .env.bak
echo "✓ .env configured"

docker exec rapidez php artisan key:generate
echo "✓ key generated"
docker exec rapidez php artisan rapidez:install --frontendonly
echo "✓ frontend dependencies copied"
docker exec rapidez yarn
echo "✓ frontend dependencies installed"
docker exec rapidez yarn run prod
echo "✓ frontend dependencies build"
docker exec rapidez php artisan storage:link
echo "✓ storage linked"
docker exec rapidez php artisan rapidez:index
echo "✓ products indexed"
docker exec rapidez php artisan vendor:publish --provider "Rapidez\Core\RapidezServiceProvider"
echo "✓ configs published"

docker compose up -d

echo ""
echo "🎉 Rapidez is ready!"
echo ""
echo "Rapidez frontend:"
echo "http://rapidez.localhost:$TRAEFIK_PORT"
echo ""
echo "Magento admin:"
echo "http://magento.localhost:$TRAEFIK_PORT/admin"
echo ""
echo "To stop everything:"
echo "docker compose down"
echo ""
echo "From here:"
echo "- Have a look, and make some changes in the templates: /resources/views/vendor/rapidez/"
echo "- Check the docs @ https://docs.rapidez.io"
echo "- Questions? Join Slack @ https://rapidez.io/slack"
