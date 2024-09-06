# Changelog 

[Unreleased changes](https://github.com/rapidez/core/compare/2.9.2...2.9.2)
## [2.9.2](https://github.com/rapidez/core/releases/tag/2.9.2) - 2024-09-03

### Fixed

- Sliderover header button fix (#554)
- Checkout progressbar steps correct fallback (#555)

## [2.9.1](https://github.com/rapidez/core/releases/tag/2.9.1) - 2024-08-27

### Fixed

- Allow UUID's in Elasticsearch (#545)
- Don't always assume api calls return json (#544)
- Correct slideover class merging (#548)
- Correct ReCaptcha header in the GraphQL mutation component (#549)
- Correct data for the order id for the checkout-payment-saved event (#550)
- Correctly use parseInt (#551)

## [2.9.0](https://github.com/rapidez/core/releases/tag/2.9.0) - 2024-08-16

### Added

- Health check for super attributes (#535)
- Category index scopes Eventy filter (#537)

### Changed

- Removed the NoAttributesToSelectSpecifiedException (#543)

### Fixed

- Prevent price addition errors in sliders (#536)
- Always attempt to decode attribute values (#520)

## [2.8.0](https://github.com/rapidez/core/releases/tag/2.8.0) - 2024-07-24

### Added

- Notifications from the session (#518)
- Order item product relation (#533)

### Fixed

- Graphql Mutation component typo (#529)
- Set error message on GraphQL errors as well (#531)
- Redirect all redirect type rewrites (#534)

## [2.7.0](https://github.com/rapidez/core/releases/tag/2.7.0) - 2024-07-15

### Added

- Auto select configurable and product option setting (#515)
- Backorders info messages (#516)
- Add fixed product taxes in cart (#517)
- Customer group relation (#528)

### Fixed

- Refactored GraphQL exceptions for error callbacks (#513)
- Slider array check (#510)
- Fix sidebar (#521)
- Use computed shipping address in estimateshippingmethod (#522)
- Prevent errors if add_to_cart config is missing (#526)
- Fix missing attributeValues and autoselect config errors (#530)

## [2.6.0](https://github.com/rapidez/core/releases/tag/2.6.0) - 2024-06-04

### Added

- Checkout step and success events (#506)
- Dusk selectors for coupons (#511)

### Fixed

- Reset styling of the listing component (#463)
- Convert the object to an array (#509)

## [2.5.0](https://github.com/rapidez/core/releases/tag/2.5.0) - 2024-05-28

### Added

- Laravel 11 support (#504)

## [2.4.1](https://github.com/rapidez/core/releases/tag/2.4.1) - 2024-05-22

### Fixed

- Revert "Rearranged the button classes (#460)" (#499)
- Round prices before multiplying by qty (#480)
- Sort images in gallery by position in Magento (#500)
- Correct rapidez/menu config path location (8e67b22)
- Check correct customer id when jwt is used (#502)

## [2.4.0](https://github.com/rapidez/core/releases/tag/2.4.0) - 2024-05-15

### Added

- Macroable models (#483)
- Multiselect product option support (#484)
- Rapidez model health check (#489)

### Changed

- Use lateral join in ForCurrentStoreScope (#478)
- Rearranged the button classes (#460)
- Refactored the slideover component (#464)
- Allow for separate slider & child containers (#495)
- Checkout agreements from the database instead of GraphQL (#492)
- Enable product autocomplete highlighting (#496)

### Fixed

- Also calculate product price on selected product option changes (#485)
- Reverted "Fix adding configurable product to cart (#461)" (#486)
- Let the OptionSwatch extend the Rapidez base model (#487)
- Use Rapidez base model for all models (#488)
- Also save product views in the report_event table (#490)
- Prevent ambiguous error when overwriting models (#498)
- Don't use global scopes for quote existence check (#493)

## [2.3.0](https://github.com/rapidez/core/releases/tag/2.3.0) - 2024-04-30

### Changed

- Use script to get the positions from the flattened type (#459)
- Do not index the search overview page (#481)

### Fixed

- Fixes for swatch filter (#477)
- Allow dataFilter null in the indexer (#470)
- Correct key in autocomplete products loop (#465)
- Only allow certain types to be searchable in autocomplete (#476)

## [2.2.0](https://github.com/rapidez/core/releases/tag/2.2.0) - 2024-04-16

### Added

- Fire postcode change event in checkout (#447)

### Fixed

- Find flat attributes by checking the actual table schema (#452)
- Fix entity_id in grouped products (#456)
- Capitalize checkCompadreVersion function properly (#457)
- Fix adding configurable product to cart (#461)
- Fix highlight retrieval (#468)
- Fix autocomplete fetch (#472)
- Fixed disabled options when no options are available (#475)

### Removed

- Remove unnecessary toArray calls in product indexer (#471)

## [2.1.0](https://github.com/rapidez/core/releases/tag/2.1.0) - 2024-03-06

### Changed

- Use ReactiveSearch fork with fixes (#435)
- Replaced cviebrock/laravel-elasticsearch with mailerlite/laravel-elasticsearch (#445)

### Fixed

- Allow shipping method to be empty (#432)
- Store and authorization header by default on Rapidez API calls (#433)
- Notify correctly in interactWithUser (#434)
- Fallback on default checkout_steps (#439)
- Send full address to check shipping methods (#441)
- Cache yarn dependencies with Dusk tests (#442)
- Move resizeobserver to nextTick function (#444)

## [2.0.0](https://github.com/rapidez/core/releases/tag/2.0.0) - 2024-02-21

In this release we refactored the cart from the Magento API to GraphQL! You should review all changes!
- Dropped support for Magento 2.4.4, Laravel 9 and PHP 8.0
- Axios is removed and you should remove it from your project and migrate to fetch, we've added some helpers you could use:
    - `rapidezAPI(method, endpoint, data = {}, options = {})`
    - `magentoGraphQL(query, variables = {}, options = { headers: {}, redirectOnExpiration: true, notifyOnError: true })`
    - `magentoAPI(method, endpoint, data = {}, options = { headers: {}, redirectOnExpiration: true, notifyOnError: true })`
- `cart_attributes` option is added to `config/rapidez/frontend.php` if you like to add some extra attributes on the cart items
- `Cart.vue` and `GetCart.js` removed; if you did override this you should refactor it to the new cart store; you can just use `cart` or `$root.cart`
- `Coupon.vue` is removed and migrated to the GraphQL mutation component
- GraphQL component callback parameters changed; the first parameter is now the data and the second the response to keep it inline with the GraphQL mutation component.
- You should review all template changes
- If you're using product option file uploads you need to install https://github.com/rapidez/magento2-compadre in Magento

### Added

- Output design/head/includes (#428)

### Changed

- Migrated the cart from the Magento API to GraphQL (#372)
- Drop Laravel 9 and PHP 8.0 support (#429)

### Fixed

- Properly set slide behavior in slider (#430)

## [1.13.0](https://github.com/rapidez/core/releases/tag/1.13.0) - 2024-02-20

### Changed

- Upgrade Turbo for link preloading, show progressbar quicker (#426)

### Fixed

- Clear swatches & attributes on store change (#424)
- Use hasAny in button base Blade component (#425)

## [1.12.0](https://github.com/rapidez/core/releases/tag/1.12.0) - 2024-02-16

### Added

- Catalog urls for products and categories (#419)
- Select cataloginventory_stock_item.manage_stock (#423)

### Changed

- Removed hardcoded checkout step numbers (#420)

## [1.11.0](https://github.com/rapidez/core/releases/tag/1.11.0) - 2024-02-05

### Changed

- Make positions flattened (#415)
- Use lowercase & asciifolding filters in default analyzer & in synonym analyzer (#416)
- Seperate check whether flat tables are active, and if they exist (#413)


## [1.10.2](https://github.com/rapidez/core/releases/tag/1.10.2) - 2024-01-31

### Fixed

 - Watch currentShippingMethod instead of shipping_method (#414)


## [1.10.1](https://github.com/rapidez/core/releases/tag/1.10.1) - 2024-01-30

### Fixed

 - Fix slot scope "limit" to "size" (#412)

## [1.10.0](https://github.com/rapidez/core/releases/tag/1.10.0) - 2024-01-29

### Added

 - Add new storecode if directive (#411)
 - Add product parent (#404)
 - Improve flexibility of additional search queries in autocomplete (#407)

### Changed

- Always set step in checkout success to success step (#403)
- Refresh user before retrieving addresses (#345)

### Fixed

 - Fix usage of whitespace tokenizer (#410)


## [1.9.0](https://github.com/rapidez/core/releases/tag/1.9.0) - 2024-01-24

### Changed

- Use the configured locale from Magento (#409)

## [1.8.0](https://github.com/rapidez/core/releases/tag/1.8.0) - 2024-01-23

### Added

- New payment events (#406)
- Robots.txt from Magento config (#408)

### Fixed

- Use stores to get the token and mask on checkout success (#377)
- Fix dusk tests (#405)

## [1.7.1](https://github.com/rapidez/core/releases/tag/1.7.1) - 2024-01-05

### Fixed

- Exclude register themes when running from console (#401)

## [1.7.0](https://github.com/rapidez/core/releases/tag/1.7.0) - 2024-01-04

### Added

- Payment icons (#374)
- Store set event (#398)
- Product review summary model (#399)
- GraphQL components store prop (#400)

### Fixed

- Trigger mounted event for autocomplete to remove timing dependency (#395)

## [1.6.1](https://github.com/rapidez/core/releases/tag/1.6.1) - 2023-12-08

### Fixed

- Use configured category model for subcategories (#396)

## [1.6.0](https://github.com/rapidez/core/releases/tag/1.6.0) - 2023-12-07

### Added

- Store code route middleware (#394)

### Fixed

- Correct cross-sells field (#392)
- Only add super attributes if they're also in the flat table (#393)

## [1.5.0](https://github.com/rapidez/core/releases/tag/1.5.0) - 2023-12-05

### Added

- Search synonyms support (#387)

### Fixed

- Turbo update and workaround for 404 pages (#388)
- Fix slider reactivity when resized (#349)

## [1.4.1](https://github.com/rapidez/core/releases/tag/1.4.1) - 2023-11-24

### Fixed

- Swatches fetch loop bug (#385)
- Improve slider performance (#384)
- Throw exception when the store is not found (#386)

## [1.4.0](https://github.com/rapidez/core/releases/tag/1.4.0) - 2023-11-22

### Added

- Support for image disabled option (#381)
- Position per category support (#382)

### Fixed

- Clear cart storage when not found (#383)

## [1.3.0](https://github.com/rapidez/core/releases/tag/1.3.0) - 2023-11-14

### Added

- Added special price and priceValidUntil to microdata (#380)

### Fixed

- Support MSP way of saving method_title (#379)

## [1.2.0](https://github.com/rapidez/core/releases/tag/1.2.0) - 2023-11-07

### Added

- Implemented customer address prefix, suffix, vat_id and fax (#373)

### Fixed

- Use new config path (#375)

## [1.1.0](https://github.com/rapidez/core/releases/tag/1.1.0) - 2023-11-03

### Changed

- Indexer config with visibility option (#369)

## [1.0.1](https://github.com/rapidez/core/releases/tag/1.0.1) - 2023-11-01

### Fixed

- Avoid error in console (#365)
- Set mutating to false in a finally (#366)
- Remove old button component (#367)
- Use dragOnClick and disable keyboard for the price filter (#368)
- Use data_get to allow both stdClass & arrays to be used (#370)
- Update prices when super attribute option is changed (#371)

## [1.0.0](https://github.com/rapidez/core/releases/tag/1.0.0) - 2023-10-19

### Added

- Improved frontend (#307, #359)
- Custom product options (9b62a67, #282, 2f6dc83, e3e5d57, #318, a9c6ff6, 4e21f2e, 7c1d529, #327, ebd5b85)
- Magento_JwtUserToken support (#338)
- Categories index command (#280)
- Categories in the autocomplete (#298, #364)
- Alternate hreflang tags (#302)
- Magento customer auth guard (#355)
- Products relation from categories (#353)
- Product view report support (c2f869d, 2e97a44)
- Implement healthchecks (#341)
- VAT field on addresses (#315)
- Additional filters possibility (#326)

### Changed

- Implement VueUse (#183, #229, #231, #269, #290, #300, #330, #331, #340)
- Move config to files in Rapidez folder (#356)
- Heroicons v2 update (#304)
- Generalized functionality for various indexer commands (#254)
- Uniform model naming (e9825ed)
- Remove column aliases and qualify columns (#296, eeb2e2e)
- Expose entire Vue component through a scoped slot (#333)
- Cleaner price fallback code (cf1f5bc, 6522163)
- Html lang attribute from the Magento config (8ee136e)
- Computed loggedIn (#271)
- Renamed checkout form to address (c747391)

### Fixed

- Honor the cataloginventory/options/show_out_of_stock setting (b16ab18)
- Wrap raw query parts (#352)
- Make slidesTotal a computed property (#346)
- Passive listeners and key instead of keyCode in product image component (#350)
- Translatable cart + checkout title (7a6c533, 2a68e8b)

## [0.81.0](https://github.com/rapidez/core/releases/tag/0.81.0) - 2023-02-21

### Added

- Expose logout slot from login component (#216)

### Changed

- Prefix images coming from Magento (#213)

### Fixed

- Catch exceptions resembling 404 (#212)

## [0.80.1](https://github.com/rapidez/core/releases/tag/0.80.1) - 2023-02-17

### Fixed

- Do not trigger the logout event when there is no token (cb72bb8)

## [0.80.0](https://github.com/rapidez/core/releases/tag/0.80.0) - 2023-02-16

### Added

- Ability to remove fallback routes (#206)

### Fixed

- Logout completely when user refresh fails due a 401 (621501f)

## [0.79.2](https://github.com/rapidez/core/releases/tag/0.79.2) - 2023-02-01

### Fixed

- Replaced old helper with facade (35b545a)

## [0.79.1](https://github.com/rapidez/core/releases/tag/0.79.1) - 2023-02-01

### Fixed

- Improved Dusk tests (#201)
- Raise Dusk test default idle timeout to 2 minutes (#205)

## [0.79.0](https://github.com/rapidez/core/releases/tag/0.79.0) - 2023-01-31

### Changed

- Vue 2.7 update (#202)

### Fixed

- Load Vue as ES Module (94fa925)
- Replaced deprecated Turbo.clearCache (a8740d9)
- Prevent full page reload when navigating from listing (#200)

## [0.78.0](https://github.com/rapidez/core/releases/tag/0.78.0) - 2023-01-20

### Changed

- Renamed subcategories and added subcategories (#187)

## [0.77.0](https://github.com/rapidez/core/releases/tag/0.77.0) - 2023-01-18

### Added

- Log notifications if debug mode is on (#186)

### Changed

- Migrate from turbolinks to @hotwired/turbo (#185)
- Add changelog action (fc21c79)

### Fixed

- Browser test fixes (#181)

## [0.76.4](https://github.com/rapidez/core/releases/tag/0.76.4) - 2023-01-10

### Fixed

- Bugfix (d947dd9)

## [0.76.3](https://github.com/rapidez/core/releases/tag/0.76.3) - 2023-01-10

### Fixed

- Get the attributes values for non-swatch super attributes (6c406a2, 8ed98ad)

## [0.76.2](https://github.com/rapidez/core/releases/tag/0.76.2) - 2023-01-06

### Fixed

- Select meta description with categories (1a11268)

## [0.76.1](https://github.com/rapidez/core/releases/tag/0.76.1) - 2023-01-03

### Fixed

- `custom_attributes` in address data (#177)
- Fix key must be object if mappings or settings are empty (#180)

## [0.76.0](https://github.com/rapidez/core/releases/tag/0.76.0) - 2022-12-14

### Added

- Eventy product index settings filter (a172022)

### Changed

- Removed Magento 2.4.2 and 2.4.3 support (6994db0)

## [0.75.0](https://github.com/rapidez/core/releases/tag/0.75.0) - 2022-12-02

### Changed

- Migrated from Laravel Mix to Vite (#172)

## [0.74.0](https://github.com/rapidez/core/releases/tag/0.74.0) - 2022-11-17

### Added

- Add option to allow vertical slides (#168)
- Add option to add a link to notifications (#169)

### Changed

- Better fallback routing (#166)
- Replace old snap class with tailwind (#160)

### Fixed

- Fix ambiguous column (#170)
- Reindex url error fix (fbe1f32)

## [0.73.0](https://github.com/rapidez/core/releases/tag/0.73.0) - 2022-10-27

### Changed

- Always add a canonical with the current url without query strings (c03d794)

## [0.72.0](https://github.com/rapidez/core/releases/tag/0.72.0) - 2022-10-27

### Changed

- Generalise scroll to, to allow arbitrary selectors (#159)
- Improve code readability (#164)

### Fixed

- Fallback to using option code if its not an id (#163)
- Fixed empty customer address error in the checkout (8322cc0)

## [0.71.1](https://github.com/rapidez/core/releases/tag/0.71.1) - 2022-10-10

### Fixed

- Install tests command fixes (b95b0aa)

## [0.71.0](https://github.com/rapidez/core/releases/tag/0.71.0) - 2022-10-06

### Added

- Install tests command (72da9c1)

## [0.70.5](https://github.com/rapidez/core/releases/tag/0.70.5) - 2022-10-03

#### Added

- Added option to change store based on get param (#162)

## [0.70.4](https://github.com/rapidez/core/releases/tag/0.70.4) - 2022-10-03

### Fixed

- Get new shipping price on country change (#161)

## [0.70.3](https://github.com/rapidez/core/releases/tag/0.70.3) - 2022-09-01

### Fixed

- Make attribute_code more explicit (#155)
- Fix for from/to dates special prices (#156)

## [0.70.2](https://github.com/rapidez/core/releases/tag/0.70.2) - 2022-08-30

### Fixed

- Search results page query fix (785daf4)

## [0.70.1](https://github.com/rapidez/core/releases/tag/0.70.1) - 2022-08-24

### Fixed

- Send the store id when creating a customer (7c174e3)

## [0.70.0](https://github.com/rapidez/core/releases/tag/0.70.0) - 2022-08-23

### Added

- Save the website id in the config when indexing (c37d9ca)

## [0.69.0](https://github.com/rapidez/core/releases/tag/0.69.0) - 2022-08-18

### Added

- Graph Mutation component watch prop (18ff552)

### Fixed

- Remove error parameters from successful response (#154)

## [0.68.0](https://github.com/rapidez/core/releases/tag/0.68.0) - 2022-08-16

### Added

- Scroll to top when paginating (22257c8)
- Open Graph product data (3e2cdb1)

### Fixed

- Added the peer dependencies (1c21335)
- Removed old prop (8b57d7a)

## [0.67.0](https://github.com/rapidez/core/releases/tag/0.67.0) - 2022-08-09

### Added

- Min sale qty support (#148, #149)
- Added params from errors to the notify function (#151)

### Fixed

- Checkout steps fallback (#150)
- Fixed console errors on category page (#152)
- Change this. to window.app (#153)

## [0.66.1](https://github.com/rapidez/core/releases/tag/0.66.1) - 2022-07-22

### Fixed

- Honor the visibility attribute (#147)

## [0.66.0](https://github.com/rapidez/core/releases/tag/0.66.0) - 2022-07-22

### Added

- Added Vue cookies by default for the GTM package (264555d)
- Foot stack (aa0bfb4)
- Cart and checkout events (c6cc9a5)

### Fixed

- Index error fix (#146)

## [0.65.3](https://github.com/rapidez/core/releases/tag/0.65.3) - 2022-07-20

### Fixed

- Category table prefix fix (9870520)
- Console error fix (bab1ec2)

## [0.65.2](https://github.com/rapidez/core/releases/tag/0.65.2) - 2022-07-20

### Fixed

- Category url from url rewrite table (5e0648e)

## [0.65.1](https://github.com/rapidez/core/releases/tag/0.65.1) - 2022-07-18

### Fixed

- Expire cart for 404 addToCart response (#145)


## [0.65.0](https://github.com/rapidez/core/releases/tag/0.65.0) - 2022-07-08

### Changed

- Checkout steps per store (#142)

### Fixed

- Dusk tests fix (1133c93)
- Fixed aria labels (#143)
- Fix multiple reactive search requests (#144)

## [0.64.2](https://github.com/rapidez/core/releases/tag/0.64.2) - 2022-06-28

### Fixed

- Fallback fix when there are no children (53ece8b)

## [0.64.1](https://github.com/rapidez/core/releases/tag/0.64.1) - 2022-06-24

### Fixed

- Added fail safe if redirect type is not set (#140)

## [0.64.0](https://github.com/rapidez/core/releases/tag/0.64.0) - 2022-06-24

### Added

- GraphQL Mutation component mutating prop (#139)

## [0.63.0](https://github.com/rapidez/core/releases/tag/0.63.0) - 2022-06-21

### Added

- Magento error params in error notifications (#138)

### Fixed

- Table alias for on grouped scope (97d1a05)

## [0.62.4](https://github.com/rapidez/core/releases/tag/0.62.4) - 2022-06-07

### Fixed

- Fix return type (#137)
- Use meta instead of link in microdata to prevent loading (8d0f572)

## [0.62.3](https://github.com/rapidez/core/releases/tag/0.62.3) - 2022-06-03

### Fixed

- Coupon notifications (d1f745c)

## [0.62.2](https://github.com/rapidez/core/releases/tag/0.62.2) - 2022-06-03

### Fixed

- Notifications instead of alerts (4057543)

## [0.62.1](https://github.com/rapidez/core/releases/tag/0.62.1) - 2022-06-01

### Fixed

- Per page config parse fix (#136)

## [0.62.0](https://github.com/rapidez/core/releases/tag/0.62.0) - 2022-05-31

### Added

- Results per page filter (#134)
- Newest sort option and additionalSorting prop (#135)

## [0.61.1](https://github.com/rapidez/core/releases/tag/0.61.1) - 2022-05-24

### Fixed

- Test against Magento 2.4.4 (934d84b)
- Attribute cache per store (774101a)

## [0.61.0](https://github.com/rapidez/core/releases/tag/0.61.0) - 2022-05-17

### Added

- Better search results and field weight support (41bbc68)
- Reactive Search lazy loading (5b6da60, af6e467, c75c217)
- Preload listing.js on listings (9fd8a39, 032ef34)

### Fixed

- Extract JS chunks to the correct directory (6f4b967)

## [0.60.1](https://github.com/rapidez/core/releases/tag/0.60.1) - 2022-05-10

### Fixed

- Swapping indexes without a mapping fix (#133)

## [0.60.0](https://github.com/rapidez/core/releases/tag/0.60.0) - 2022-05-10

### Changed

- Index command abstraction (#132)

## [0.59.3](https://github.com/rapidez/core/releases/tag/0.59.3) - 2022-05-10

### Fixed

- Send Store header with GraphQL requests (#131)
- Return false on non existing option values (7de6633)

## [0.59.2](https://github.com/rapidez/core/releases/tag/0.59.2) - 2022-05-02

### Fixed

- Revert calling init immediately (db86662)

## [0.59.1](https://github.com/rapidez/core/releases/tag/0.59.1) - 2022-04-28

### Fixed

- Removed unneeded use (2d07b35)

## [0.59.0](https://github.com/rapidez/core/releases/tag/0.59.0) - 2022-04-28

### Added

- GraphQL mutation beforeRequest prop (#130)
- Price helper (0946924)

### Changed

- Do not lazy load the first 4 images on categories (0bf6762)

## [0.58.0](https://github.com/rapidez/core/releases/tag/0.58.0) - 2022-04-24

### Changed

- Use the Magento customer configuration (#127)
- Category page Lighthouse improvements (#129)

### Fixed

- Slider scroll fix (5728dea)

## [0.57.0](https://github.com/rapidez/core/releases/tag/0.57.0) - 2022-04-20

### Added

- Category filter (#128)

### Changed

- Added an additional wrapper div (a7fea71)

## [0.56.2](https://github.com/rapidez/core/releases/tag/0.56.2) - 2022-04-15

### Fixed

- Emit event fix (1bfb884)

## [0.56.1](https://github.com/rapidez/core/releases/tag/0.56.1) - 2022-04-14

### Fixed

- Encode search term in the url (0e44230)

## [0.56.0](https://github.com/rapidez/core/releases/tag/0.56.0) - 2022-04-13

### Changed

- Copied and renamed the lazy component and use requestIdleCallback (11cd9be)
- Load lazy component automatically after 3 seconds instead of requestIdleCallback (3231f20)
- Only load the user component when toggled (d7d6969, 380a23f)
- Do not chunk the product image component (850ee34)
- Only load the search autocomplete on focus (8067825)

## [0.55.0](https://github.com/rapidez/core/releases/tag/0.55.0) - 2022-04-12

### Changed

- Moved the facade (7373ab2)

## [0.54.0](https://github.com/rapidez/core/releases/tag/0.54.0) - 2022-04-11

### Changed

- Laravel 9 upgrade (ba35831, 0075076)
- Productlist and newsletter component lazy loading (db59387, 7830ee9)

### Fixed

- Save grouped products as flattened in ES (#126)

## [0.53.0](https://github.com/rapidez/core/releases/tag/0.53.0) - 2022-04-06

### Added

- Theme support (#122)

### Changed

- Product images Lighthouse improvements and dropped the jpg fallback (5fa6aef)

### Fixed

- Cache busting for Webpack chunks (88f79aa)

## [0.52.1](https://github.com/rapidez/core/releases/tag/0.52.1) - 2022-04-04

### Fixed

- Better variable naming (2d981d2)
- Use product model table everywhere (#124)
- Special prices without dates fix (#125)

## [0.52.0](https://github.com/rapidez/core/releases/tag/0.52.0) - 2022-03-29

### Added

- ViewOnly widget (#119)
- Automatic changelog (#121)

## [0.51.0](https://github.com/rapidez/core/releases/tag/0.51.0) - 2022-03-29

### Added

- Added callback in close function (#120)
- Expose stock qty option (ccc1f51)
- Website code availability in the config (034f491)

## [0.50.0](https://github.com/rapidez/core/releases/tag/0.50.0) - 2022-03-25

### Added

- Grouped products support (615ce9c, 8a2d6da, 573b87a)

### Changed

- Tailwind 3 upgrade (03735eb, d96206e)

## [0.49.2](https://github.com/rapidez/core/releases/tag/0.49.2) - 2022-03-18

### Fixed

- Check on source_model to determine the right column (cbd708d)

## [0.49.1](https://github.com/rapidez/core/releases/tag/0.49.1) - 2022-03-17

### Fixed

- Reset variables to initial variables on clear within GraphQL mutation (5f3e320)

## [0.49.0](https://github.com/rapidez/core/releases/tag/0.49.0) - 2022-03-16

### Changed

- Move object key names in swatch to better clarify the functionality (#118)
- Removed the changes GraphQL mutation option in favor of variables (3fbafed)

## [0.48.2](https://github.com/rapidez/core/releases/tag/0.48.2) - 2022-03-15

### Fixed

- Select the right column when the attribute type is a integer (e5262bf)

## [0.48.1](https://github.com/rapidez/core/releases/tag/0.48.1) - 2022-03-14

### Fixed

- Console error and swatches fix (#117)

## [0.48.0](https://github.com/rapidez/core/releases/tag/0.48.0) - 2022-03-04

### Changed

- Javascript size improvements (#116)

### Fixed

- Price slider fix (#115)
- Sort attribute options (#113)

## [0.47.1](https://github.com/rapidez/core/releases/tag/0.47.1) - 2022-03-02

### Fixed

- Cache the product model casts (7db518a)

## [0.47.0](https://github.com/rapidez/core/releases/tag/0.47.0) - 2022-02-28

### Changed

- Expose the getTotalsInformation method (ccf6c52)

### Fixed

- Remove unneeded article tag (43b4fb2)
- Raised the browser test wait times (994d4ca, 97ee47b)

## [0.46.0](https://github.com/rapidez/core/releases/tag/0.46.0) - 2022-02-28

### Changed

- Refresh totals on shipping method change (e66133a)

## [0.45.0](https://github.com/rapidez/core/releases/tag/0.45.0) - 2022-02-25

### Added

- Custom url rewrite support (af7d617)

## [0.44.0](https://github.com/rapidez/core/releases/tag/0.44.0) - 2022-02-17

### Added

- Cart events (#107)
- Made window available everywhere (#110)

### Changed

- Updated the Docker Compose Magento version to 2.4.3-p1 (0947a4a)
- Logout on GraphQL authorization errors (965fa35)
- Watch the variables prop on the GraphQL mutation component (572cfb4)

### Fixed

- Remove created index in case of unsuccessful reindex (#112)
- Corrected the logged in event order (15e7283)

## [0.43.0](https://github.com/rapidez/core/releases/tag/0.43.0) - 2022-02-11

### Added

- Logged in event (#108)

## [0.42.0](https://github.com/rapidez/core/releases/tag/0.42.0) - 2022-02-09

### Added

- Checkout address select (91efbe4, e47e9ce)

### Changed

- Logout event and clear addresses on logout (#105)

## [0.41.0](https://github.com/rapidez/core/releases/tag/0.41.0) - 2022-02-09

### Changed

- Use primary key in the ForCurrentStoreScope (#104)
- GraphQL component runQuery slot scope (#106)
- Dynamic country with default country fallback (#100)

## [0.40.0](https://github.com/rapidez/core/releases/tag/0.40.0) - 2022-02-05

### Added

- Robots yield (#101)

## [0.39.2](https://github.com/rapidez/core/releases/tag/0.39.2) - 2022-02-01

### Fixed

- Syntax error fix (becaece)

## [0.39.1](https://github.com/rapidez/core/releases/tag/0.39.1) - 2022-01-31

### Fixed

- Undefined request fix (c510cc7)
- Recaptcha component location prop (9338284)

## [0.39.0](https://github.com/rapidez/core/releases/tag/0.39.0) - 2022-01-28

### Added

- Custom sorting label translations possibility (#98)
- Quote prices exclusive tax (1c6f885)

## [0.38.1](https://github.com/rapidez/core/releases/tag/0.38.1) - 2022-01-18

### Fixed

- Handle case when no category id's returned (#96)

## [0.38.0](https://github.com/rapidez/core/releases/tag/0.38.0) - 2022-01-14

### Added

- Login component redirect prop (71617ae)

### Fixed

- Eventy filters are applied correctly again (d2450e5)

## [0.37.0](https://github.com/rapidez/core/releases/tag/0.37.0) - 2022-01-12

### Changed

- Possibility to change the GraphQL data from the callback (3dfef56)

## [0.36.0](https://github.com/rapidez/core/releases/tag/0.36.0) - 2022-01-11

### Changed

- Productpage scopes filter, renamed the frontend attributes filter and small refactor (dbadbeb)

## [0.35.0](https://github.com/rapidez/core/releases/tag/0.35.0) - 2022-01-11

### Added

- Recaptcha component (6868b63)

### Fixed

- Price react to the new query filter (ac6b74e)

## [0.34.0](https://github.com/rapidez/core/releases/tag/0.34.0) - 2022-01-04

### Added

- Quote items select Eventy filter (#95)

## [0.33.0](https://github.com/rapidez/core/releases/tag/0.33.0) - 2022-01-04

### Added

- Expired cart check (6382c7e)

## [0.32.1](https://github.com/rapidez/core/releases/tag/0.32.1) - 2021-12-22

### Fixed

- Filter widgets by their assigned store id (#94)

## [0.32.0](https://github.com/rapidez/core/releases/tag/0.32.0) - 2021-12-16

### Added

- Add to cart callback prop (5710843)

## [0.31.6](https://github.com/rapidez/core/releases/tag/0.31.6) - 2021-12-15

### Fixed

- Round microdata prices (494a1c2)

## [0.31.5](https://github.com/rapidez/core/releases/tag/0.31.5) - 2021-12-14

### Fixed

- Use booting instead of booted in models (2dbb2b7)

## [0.31.4](https://github.com/rapidez/core/releases/tag/0.31.4) - 2021-12-14

### Fixed

- Override quote select fix (#93)

## [0.31.3](https://github.com/rapidez/core/releases/tag/0.31.3) - 2021-12-14

### Fixed

- Prefix column with table to prevent ambiguous columns (#92)

## [0.31.2](https://github.com/rapidez/core/releases/tag/0.31.2) - 2021-12-13

### Fixed

- Keep the billing credentials in local storage (7fb81af)

## [0.31.1](https://github.com/rapidez/core/releases/tag/0.31.1) - 2021-12-10

### Fixed

- Return false instead of null in cache functions as null can not be cached (f8c56fe)

## [0.31.0](https://github.com/rapidez/core/releases/tag/0.31.0) - 2021-12-10

### Changed

- Renamed qty prop to defaultQty (a541a96)

## [0.30.4](https://github.com/rapidez/core/releases/tag/0.30.4) - 2021-12-09

### Fixed

- Only use qty increments when it is enabled (f1a2356)

## [0.30.3](https://github.com/rapidez/core/releases/tag/0.30.3) - 2021-12-03

### Fixed

- Multiline widget parameter fix (7ae439e)

## [0.30.2](https://github.com/rapidez/core/releases/tag/0.30.2) - 2021-12-02

### Fixed

- Console error fix (da29a4e)

## [0.30.1](https://github.com/rapidez/core/releases/tag/0.30.1) - 2021-12-01

### Fixed

- Forgotten import (cdee1bb)

## [0.30.0](https://github.com/rapidez/core/releases/tag/0.30.0) - 2021-12-01

### Added

- Product children select Eventy filter (b94c3e7)

## [0.29.0](https://github.com/rapidez/core/releases/tag/0.29.0) - 2021-11-30

### Added

- Custom reactive prop (c3f29dc)

### Fixed

- Only search when value is not empty (#90)

## [0.28.0](https://github.com/rapidez/core/releases/tag/0.28.0) - 2021-11-25

### Added

- Toggler component callback prop (669d31b)

## [0.27.0](https://github.com/rapidez/core/releases/tag/0.27.0) - 2021-11-25

### Added

- Cart refreshed event (d5bd9df)

## [0.26.2](https://github.com/rapidez/core/releases/tag/0.26.2) - 2021-11-25

### Fixed

- Discounts where not displayed (c878109)

## [0.26.1](https://github.com/rapidez/core/releases/tag/0.26.1) - 2021-11-23

### Fixed

- Do not try to decode the product children if it is already an object (93068df)

## [0.26.0](https://github.com/rapidez/core/releases/tag/0.26.0) - 2021-11-23

### Added

- Implemented special prices (88333ae)
- Forgot password link (3f2729e)

### Fixed

- GraphQL mutation clear with variables (c872150)



## [0.25.1](https://github.com/rapidez/core/releases/tag/0.25.1) - 2021-11-16

### Fixed

- Do not try to render non implemented widgets in production (7ddaf84)

## [0.25.0](https://github.com/rapidez/core/releases/tag/0.25.0) - 2021-11-12

### Added

- Eventy filter for product attributes available in the frontend (#89)

## [0.24.1](https://github.com/rapidez/core/releases/tag/0.24.1) - 2021-11-11

### Fixed

- Show shipping costs with tax (a98fe6f)

## [0.24.0](https://github.com/rapidez/core/releases/tag/0.24.0) - 2021-11-11

### Added

- Show shipping info in cart totals (5aa383e)

### Fixed

- Only show tax in cart totals if present (affbce1)

## [0.23.0](https://github.com/rapidez/core/releases/tag/0.23.0) - 2021-11-10

### Added

- Configurable additional searchable attributes (80ec594)

### Changed

- Moved the product sku microdata (2bbb89c)

### Fixed

- Do not overwrite the category select query (230c87e)

## [0.22.0](https://github.com/rapidez/core/releases/tag/0.22.0) - 2021-11-10

### Added

- Breadcrumb rich snippets (9f7ca61)
- Product rich snippets (4492d23)

### Changed

- Use more HTML5 semantic tags (16cc5ee)

## [0.21.0](https://github.com/rapidez/core/releases/tag/0.21.0) - 2021-11-09

### Changed

- Refactored the product options in listings (860be53)

## [0.20.1](https://github.com/rapidez/core/releases/tag/0.20.1) - 2021-11-04

### Fixes

- Product url access on product pages in js (#87)

## [0.20.0](https://github.com/rapidez/core/releases/tag/0.20.0) - 2021-11-03

### Added

- GraphQL callback prop (46dcc39)

## [0.19.0](https://github.com/rapidez/core/releases/tag/0.19.0) - 2021-11-03

### Added

- Notification duration prop (#85)
- Slider reference prop (#86)

### Changed

- Redirect with configurable products and disabled swatches in listings (#84)

## [0.18.0](https://github.com/rapidez/core/releases/tag/0.18.0) - 2021-10-23

### Added

- Sensitive/encrypted config support (5def207)
- Recaptcha support in the GraphQL mutation component (8020ead)
- Stack in the head of the layout (c0bcbc1)

### Fixed

- Only show the page content heading when filled (bcf3fde)

## [0.17.0](https://github.com/rapidez/core/releases/tag/0.17.0) - 2021-10-20

### Added

- Add to cart adding/added states and notify props (3d1b012, d367430)

## [0.16.0](https://github.com/rapidez/core/releases/tag/0.16.0) - 2021-10-08

### Added

- GraphQL component prop to mutate on event (#80)

### Changed

- Kebab-case events names (#81)

## [0.15.2](https://github.com/rapidez/core/releases/tag/0.15.2) - 2021-10-07

### Changed

- Allow post requests to the admin routes (6e91d62)

### Fixed

- Slider totals fix (#78)
- Spelling fix (#79)

## [0.15.1](https://github.com/rapidez/core/releases/tag/0.15.1) - 2021-10-07

### Fixed

- GraphQL Mutation component reactivity fix (092331a)

## [0.15.0](https://github.com/rapidez/core/releases/tag/0.15.0) - 2021-10-05

### Added

- Configurable z-indexes (#76)

### Changed

- Keep the email after the checkout and pass variables to GraphQL callbacks (#77)

## [0.14.2](https://github.com/rapidez/core/releases/tag/0.14.2) - 2021-09-28

### Added

- Added the variables to the GraphQL mutation slot scope (#73)

## [0.14.1](https://github.com/rapidez/core/releases/tag/0.14.1) - 2021-09-24

### Fixed

- Hide slider dots when there is just one slide (#70)
- Clear cart on success page (#71)

## [0.14.0](https://github.com/rapidez/core/releases/tag/0.14.0) - 2021-09-22

### Added

- Slider navigation dots (#66)
- GraphQL component variables support (b0a9ed4)

### Changed

- Expose the product id to the frontend (ab28cac)
- Use localstorage email as guest email if available (#69)

## [0.13.0](https://github.com/rapidez/core/releases/tag/0.13.0) - 2021-09-21

### Added

- Qty increments in the cart (6442c1c, 8036645)
- CheckoutPaymentSaved event order data (#67)

### Changed

- Refactored the button component (dae4152)

### Fixed

- Always refresh the cart after changes (174f473)
- Resolve swatches anywhere (#68)

## [0.12.0](https://github.com/rapidez/core/releases/tag/0.12.0) - 2021-09-16

### Added

- Variable disable when loading button option (6abaae4)

### Fixed

- Allow Vue to set a href on an anchor button (6ccaefe)
- Select filters do not react on themself and cleanup (bfbe4a7)
- Always get the lowest price as base price (#65)

## [0.11.0](https://github.com/rapidez/core/releases/tag/0.11.0) - 2021-09-10

### Added

- Aria labels (b410c22)
- Width/height attributes on images (5f46d7a)

### Changed

- Bigger arrow icons on the image carousel (2db0bf9)

### Fixed

- Unique ids (cc3d9ac)

### Security

- Updated lodash (60a68bf)

## [0.10.1](https://github.com/rapidez/core/releases/tag/0.10.1) - 2021-09-07

### Fixed

- Fixed overwriting select (#62)

## [0.10.0](https://github.com/rapidez/core/releases/tag/0.10.0) - 2021-09-01

### Added

- Widget replace option (9b986bc)

## [0.9.3](https://github.com/rapidez/core/releases/tag/0.9.3) - 2021-09-01

### Changed

- Refresh cart totals when selecting a payment method (d4d8aaf, 8369ff0)

## [0.9.2](https://github.com/rapidez/core/releases/tag/0.9.2) - 2021-08-31

### Changed

- Test against Magento 2.4.2-p2 and 2.4.3 (465d430)
- Use title from options for the productlist widget (276d739, 2fd56eb)
- Use the button component for the slider buttons (9bb54e7)

## [0.9.1](https://github.com/rapidez/core/releases/tag/0.9.1) - 2021-08-27

### Fixed

- Productlist slider button position (48a3d15)

## [0.9.0](https://github.com/rapidez/core/releases/tag/0.9.0) - 2021-08-27

### Added

- Productlist slider (96f3523, 8ea1d29)

## [0.8.0](https://github.com/rapidez/core/releases/tag/0.8.0) - 2021-08-27

### Added

- Toggler component open prop (#59)
- Canonical yield (7938a6c)
- Eventy global scopes filter on all models (#60)

### Fixed

- HasEventyGlobalScopeFilter trait boot method name (#61)
- Productlist widget bugfix (269d886)

## [0.7.0](https://github.com/rapidez/core/releases/tag/0.7.0) - 2021-08-24

### Added

- Configurable widgets (#57)
- Content directive (edbf945)

## [0.6.1](https://github.com/rapidez/core/releases/tag/0.6.1) - 2021-08-17

### Fixed

- Product image thumbnail styling (2a34c4c)

## [0.6.0](https://github.com/rapidez/core/releases/tag/0.6.0) - 2021-08-17

### Added

- Product images carousel (91e4997)

## [0.5.0](https://github.com/rapidez/core/releases/tag/0.5.0) - 2021-08-13

### Added

- Breadcrumbs on products by latest category (647334e)

## [0.4.0](https://github.com/rapidez/core/releases/tag/0.4.0) - 2021-08-13

### Added

- Breadcrumbs (5db2a36)

### Fixed

- Prevent Vue warning (eb19308)

## [0.3.2](https://github.com/rapidez/core/releases/tag/0.3.2) - 2021-08-12

### Changed

- Overwritable default qty and use the qty_increments (7ac384c)

## [0.3.1](https://github.com/rapidez/core/releases/tag/0.3.1) - 2021-08-12

### Changed

- Possibility to use the refresh cart method without Vue (e37312e)
- Pass through the Axios response with the GraphQL Mutation callback (3564519)

## [0.3.0](https://github.com/rapidez/core/releases/tag/0.3.0) - 2021-08-10

### Added

- Product quantity increments (cf21993)

## [0.2.2](https://github.com/rapidez/core/releases/tag/0.2.2) - 2021-08-07

### Fixed

- Typo (1a4de04)

## [0.2.1](https://github.com/rapidez/core/releases/tag/0.2.1) - 2021-08-07

### Fixed

- Only show discount when it is not 0 (1621edb)

## [0.2.0](https://github.com/rapidez/core/releases/tag/0.2.0) - 2021-08-03

### Added

- Configurable models (#56)

## [0.1.4](https://github.com/rapidez/core/releases/tag/0.1.4) - 2021-07-20

### Changed

- Dynamic IsActiveScope column (4e97b21)

## [0.1.3](https://github.com/rapidez/core/releases/tag/0.1.3) - 2021-07-19

### Changed

- GraphQL mutation component callback prop (446d3c6)
- Possiblity to use some methods without Vue (51d6b6b)

## [0.1.2](https://github.com/rapidez/core/releases/tag/0.1.2) - 2021-07-08

### Fixed

- Image position fix (#52)

## [0.1.1](https://github.com/rapidez/core/releases/tag/0.1.1) - 2021-07-07

### Added
- WebP images from rapidez/image-resizer with fallback (#51)

## [0.1.0](https://github.com/rapidez/core/releases/tag/0.1.0) - 2021-06-23

Public beta release!

