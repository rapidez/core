## Rapidez Core

Rapidez is a headless frontend for Magento 2 built with Laravel, Vue and InstantSearch (with Elasticsearch/Opensearch). `rapidez/core` is the foundation package — it provides the Eloquent models that read the Magento database directly (for catalog, category, CMS data, etc), integrates the Magento GraphQL APIs (for cart, checkout, customer actions, etc), and supplies the base Blade views, routing, and indexing commands that a Rapidez storefront is built on.

Never treat this as a "normal" Laravel e-commerce package: catalog data is read straight from Magento's database schema via Eloquent, not through Magento's own ORM or REST API — GraphQL is only used for write/transactional actions like cart and checkout.

### Rapidez Docs

IMPORTANT!! For all Rapidez specific questions please check the docs at https://docs.rapidez.io/llms.txt and the relevant links first!

### Project Structure

```
├── src/                    # PSR-4: Rapidez\Core\
│   ├── Models/             # Eloquent models reading Magento's DB directly
│   ├── Index/              # Elasticsearch/OpenSearch indexing logic
│   ├── Actions/            # Single-purpose action classes (e.g. cart/checkout GraphQL calls)
│   ├── Http/               # Controllers, middleware, requests
│   ├── ViewComponents/     # Blade view components
│   ├── ViewDirectives/     # Custom Blade directives
│   ├── ContentVariables/   # Variables injectable into CMS content
│   ├── Widgets/            # CMS widget classes
│   ├── Events/ Listeners/  # Event/listener classes
│   ├── Commands/           # Artisan commands (install, index, validate)
│   ├── Auth/ Casts/ Enums/ Exceptions/ Facades/
├── resources/
│   ├── views/              # Blade templates (product, category, cart, checkout, ...)
│   ├── js/                 # Renderless Vue 3 components
│   ├── css/                # Tailwind CSS
│   └── boost/guidelines/   # This file — Laravel Boost AI guidelines
├── config/rapidez/         # Package config (models, routing, searchkit, jwt, ...)
├── routes/                 # web.php, api.php, magento-redirects.php
├── lang/                   # Translations (en, nl)
└── tests/
    ├── Unit/ Feature/      # PHPUnit
    └── playwright/         # End-to-end specs
```

### Architecture

- Catalog, category, CMS data, etc everything that's we need to read from the Magento → Eloquent models querying the Magento database directly (see `Rapidez\Core\Models\*`)
- Cart, checkout, customer mutations, etc everything that has authentication, is dynamic or need to write to Magento → Magento GraphQL.
- Search and category filtering → IntantSearch with Elasticsearch or OpenSearch, kept in sync via the indexing commands this package provides.
- Views are plain Blade — no `.phtml`, no Magento XML layout files.
- Vue components are renderless; separating the logic of a component from its presentation

### Technology Stack

- PHP 8+
- Laravel 12+
- Tailwind CSS 4
- Vue 3 (with renderless components)
- VueUse
- Vue InstantSearch (with Searchkit)
- Vite

### Testing

- PHPUnit `./vendor/bin/phpunit`
- PHPStan `./vendor/bin/phpstan`
- Playwright: `yarn playwright test`

### Indexing

With `php artisan rapidez:index` from the application that's using this core package.
