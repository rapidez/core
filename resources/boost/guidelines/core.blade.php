## Rapidez Core

Rapidez is a headless frontend for Magento 2 built with Laravel, Vue and InstantSearch (with Elasticsearch/Opensearch). `rapidez/core` is the foundation package — it provides the Eloquent models that read the Magento database directly (for catalog, category, CMS data, etc), integrates the Magento GraphQL APIs (for cart, checkout, customer actions, etc), and supplies the base Blade views, routing, and indexing commands that a Rapidez storefront is built on.

Never treat this as a "normal" Laravel e-commerce package: catalog data is read straight from Magento's database schema via Eloquent, not through Magento's own ORM or REST API — GraphQL is only used for write/transactional actions like cart and checkout.

IMPORTANT!! For all Rapidez specific questions please check the https://docs.rapidez.io/llms.txt and the relevant links first!

### Architecture

- Catalog, category, CMS data, etc everything that's we need to read from the Magento → Eloquent models querying the Magento database directly (see `Rapidez\Core\Models\*`)
- Cart, checkout, customer mutations, etc everything that has authentication, is dynamic or need to write to Magento → Magento GraphQL.
- Search and category filtering → IntantSearch with Elasticsearch or OpenSearch, kept in sync via the indexing commands this package provides.
- Views are plain Blade — no `.phtml`, no Magento XML layout files.
- Vue components are renderless; separating the logic of a component from its presentation
