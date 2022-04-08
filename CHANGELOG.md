# Changelog

## [Unreleased](https://github.com/org/repo/compare/0.53.0...master)

## [0.53.0](https://github.com/org/repo/compare/0.52.1...0.53.0) - 2022-04-06

### Added

- Theme support (#122)

### Changed

- Product images Lighthouse improvements and dropped the jpg fallback (5fa6aef)

### Fixed

- Cache busting for Webpack chunks (88f79aa)



## [0.52.1](https://github.com/org/repo/compare/0.52.0...0.52.1) - 2022-04-04

### Fixed

- Better variable naming (2d981d2)
- Use product model table everywhere (#124)
- Special prices without dates fix (#125)



## [0.52.0](https://github.com/org/repo/compare/0.51.0...0.52.0) - 2022-03-29

### Added

- ViewOnly widget (#119)
- Automatic changelog (#121)



## [0.51.0](https://github.com/org/repo/compare/0.50.0...0.51.0) - 2022-03-29

### Added

- Added callback in close function (#120)
- Expose stock qty option (ccc1f51)
- Website code availability in the config (034f491)



## [0.50.0](https://github.com/org/repo/compare/0.49.2...0.50.0) - 2022-03-25

### Added

- Grouped products support (615ce9c, 8a2d6da, 573b87a)

### Changed

- Tailwind 3 upgrade (03735eb, d96206e)



## [0.49.2](https://github.com/org/repo/compare/0.49.1...0.49.2) - 2022-03-18

### Fixed

- Check on source_model to determine the right column (cbd708d)



## [0.49.1](https://github.com/org/repo/compare/0.49.0...0.49.1) - 2022-03-17

### Fixed

- Reset variables to initial variables on clear within GraphQL mutation (5f3e320)



## [0.49.0](https://github.com/org/repo/compare/0.48.2...0.49.0) - 2022-03-16

### Changed

- Move object key names in swatch to better clarify the functionality (#118)
- Removed the changes GraphQL mutation option in favor of variables (3fbafed)



## [0.48.2](https://github.com/org/repo/compare/0.48.1...0.48.2) - 2022-03-15

### Fixed

- Select the right column when the attribute type is a integer (e5262bf)



## [0.48.1](https://github.com/org/repo/compare/0.48.0...0.48.1) - 2022-03-14

### Fixed

- Console error and swatches fix (#117)



## [0.48.0](https://github.com/org/repo/compare/0.47.1...0.48.0) - 2022-03-04

### Changed

- Javascript size improvements (#116)

### Fixed

- Price slider fix (#115)
- Sort attribute options (#113)



## [0.47.1](https://github.com/org/repo/compare/0.47.0...0.47.1) - 2022-03-02

### Fixed

- Cache the product model casts (7db518a)



## [0.47.0](https://github.com/org/repo/compare/0.46.0...0.47.0) - 2022-02-28

### Changed

- Expose the getTotalsInformation method (ccf6c52)

### Fixed

- Remove unneeded article tag (43b4fb2)
- Raised the browser test wait times (994d4ca, 97ee47b)



## [0.46.0](https://github.com/org/repo/compare/0.45.0...0.46.0) - 2022-02-28

### Changed

- Refresh totals on shipping method change (e66133a)



## [0.45.0](https://github.com/org/repo/compare/0.44.0...0.45.0) - 2022-02-25

### Added

- Custom url rewrite support (af7d617)



## [0.44.0](https://github.com/org/repo/compare/0.43.0...0.44.0) - 2022-02-17

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



## [0.43.0](https://github.com/org/repo/compare/0.42.0...0.43.0) - 2022-02-11

### Added

- Logged in event (#108)



## [0.42.0](https://github.com/org/repo/compare/0.41.0...0.42.0) - 2022-02-09

### Added

- Checkout address select (91efbe4, e47e9ce)

### Changed

- Logout event and clear addresses on logout (#105)



## [0.41.0](https://github.com/org/repo/compare/0.40.0...0.41.0) - 2022-02-09

### Changed

- Use primary key in the ForCurrentStoreScope (#104)
- GraphQL component runQuery slot scope (#106)
- Dynamic country with default country fallback (#100)



## [0.40.0](https://github.com/org/repo/compare/0.39.2...0.40.0) - 2022-02-05

### Added

- Robots yield (#101)



## [0.39.2](https://github.com/org/repo/compare/0.39.1...0.39.2) - 2022-02-01

### Fixed

- Syntax error fix (becaece)



## [0.39.1](https://github.com/org/repo/compare/0.39.0...0.39.1) - 2022-01-31

### Fixed

- Undefined request fix (c510cc7)
- Recaptcha component location prop (9338284)



## [0.39.0](https://github.com/org/repo/compare/0.38.1...0.39.0) - 2022-01-28

### Added

- Custom sorting label translations possibility (#98)
- Quote prices exclusive tax (1c6f885)



## [0.38.1](https://github.com/org/repo/compare/0.38.0...0.38.1) - 2022-01-18

### Fixed

- Handle case when no category id's returned (#96)



## [0.38.0](https://github.com/org/repo/compare/0.37.0...0.38.0) - 2022-01-14

### Added

- Login component redirect prop (71617ae)

### Fixed

- Eventy filters are applied correctly again (d2450e5)



## [0.37.0](https://github.com/org/repo/compare/0.36.0...0.37.0) - 2022-01-12

### Changed

- Possibility to change the GraphQL data from the callback (3dfef56)



## [0.36.0](https://github.com/org/repo/compare/0.35.0...0.36.0) - 2022-01-11

### Changed

- Productpage scopes filter, renamed the frontend attributes filter and small refactor (dbadbeb)



## [0.35.0](https://github.com/org/repo/compare/0.34.0...0.35.0) - 2022-01-11

### Added

- Recaptcha component (6868b63)

### Fixed

- Price react to the new query filter (ac6b74e)



## [0.34.0](https://github.com/org/repo/compare/0.33.0...0.34.0) - 2022-01-04

### Added

- Quote items select Eventy filter (#95)



## [0.33.0](https://github.com/org/repo/compare/0.32.1...0.33.0) - 2022-01-04

### Added

- Expired cart check (6382c7e)



## [0.32.1](https://github.com/org/repo/compare/0.32.0...0.32.1) - 2021-12-22

### Fixed

- Filter widgets by their assigned store id (#94)



## [0.32.0](https://github.com/org/repo/compare/0.31.6...0.32.0) - 2021-12-16

### Added

- Add to cart callback prop (5710843)



## [0.31.6](https://github.com/org/repo/compare/0.31.5...0.31.6) - 2021-12-15

### Fixed

- Round microdata prices (494a1c2)



## [0.31.5](https://github.com/org/repo/compare/0.31.4...0.31.5) - 2021-12-14

### Fixed

- Use booting instead of booted in models (2dbb2b7)



## [0.31.4](https://github.com/org/repo/compare/0.31.3...0.31.4) - 2021-12-14

### Fixed

- Override quote select fix (#93)



## [0.31.3](https://github.com/org/repo/compare/0.31.2...0.31.3) - 2021-12-14

### Fixed

- Prefix column with table to prevent ambiguous columns (#92)



## [0.31.2](https://github.com/org/repo/compare/0.31.1...0.31.2) - 2021-12-13

### Fixed

- Keep the billing credentials in local storage (7fb81af)



## [0.31.1](https://github.com/org/repo/compare/0.31.0...0.31.1) - 2021-12-10

### Fixed

- Return false instead of null in cache functions as null can not be cached (f8c56fe)



## [0.31.0](https://github.com/org/repo/compare/0.30.4...0.31.0) - 2021-12-10

### Changed

- Renamed qty prop to defaultQty (a541a96)



## [0.30.4](https://github.com/org/repo/compare/0.30.3...0.30.4) - 2021-12-09

### Fixed

- Only use qty increments when it is enabled (f1a2356)



## [0.30.3](https://github.com/org/repo/compare/0.30.2...0.30.3) - 2021-12-03

### Fixed

- Multiline widget parameter fix (7ae439e)



## [0.30.2](https://github.com/org/repo/compare/0.30.1...0.30.2) - 2021-12-02

### Fixed

- Console error fix (da29a4e)



## [0.30.1](https://github.com/org/repo/compare/0.30.0...0.30.1) - 2021-12-01

### Fixed

- Forgotten import (cdee1bb)



## [0.30.0](https://github.com/org/repo/compare/0.29.0...0.30.0) - 2021-12-01

### Added

- Product children select Eventy filter (b94c3e7)



## [0.29.0](https://github.com/org/repo/compare/0.28.0...0.29.0) - 2021-11-30

### Added

- Custom reactive prop (c3f29dc)

### Fixed

- Only search when value is not empty (#90)



## [0.28.0](https://github.com/org/repo/compare/0.27.0...0.28.0) - 2021-11-25

### Added

- Toggler component callback prop (669d31b)



## [0.27.0](https://github.com/org/repo/compare/0.26.2...0.27.0) - 2021-11-25

### Added

- Cart refreshed event (d5bd9df)



## [0.26.2](https://github.com/org/repo/compare/0.26.1...0.26.2) - 2021-11-25

### Fixed

- Discounts where not displayed (c878109)



## [0.26.1](https://github.com/org/repo/compare/0.26.0...0.26.1) - 2021-11-23

### Fixed

- Do not try to decode the product children if it is already an object (93068df)



## [0.26.0](https://github.com/org/repo/compare/0.25.1...0.26.0) - 2021-11-23

### Added

- Implemented special prices (88333ae)
- Forgot password link (3f2729e)

### Fixed

- GraphQL mutation clear with variables (c872150)





## [0.25.1](https://github.com/org/repo/compare/0.25.0...0.25.1) - 2021-11-16

### Fixed

- Do not try to render non implemented widgets in production (7ddaf84)



## [0.25.0](https://github.com/org/repo/compare/0.24.1...0.25.0) - 2021-11-12

### Added

- Eventy filter for product attributes available in the frontend (#89)



## [0.24.1](https://github.com/org/repo/compare/0.24.0...0.24.1) - 2021-11-11

### Fixed

- Show shipping costs with tax (a98fe6f)



## [0.24.0](https://github.com/org/repo/compare/0.23.0...0.24.0) - 2021-11-11

### Added

- Show shipping info in cart totals (5aa383e)

### Fixed

- Only show tax in cart totals if present (affbce1)



## [0.23.0](https://github.com/org/repo/compare/0.22.0...0.23.0) - 2021-11-10

### Added

- Configurable additional searchable attributes (80ec594)

### Changed

- Moved the product sku microdata (2bbb89c)

### Fixed

- Do not overwrite the category select query (230c87e)



## [0.22.0](https://github.com/org/repo/compare/0.21.0...0.22.0) - 2021-11-10

### Added

- Breadcrumb rich snippets (9f7ca61)
- Product rich snippets (4492d23)

### Changed

- Use more HTML5 semantic tags (16cc5ee)



## [0.21.0](https://github.com/org/repo/compare/0.20.1...0.21.0) - 2021-11-09

### Changed

- Refactored the product options in listings (860be53)



## [0.20.1](https://github.com/org/repo/compare/0.20.0...0.20.1) - 2021-11-04

### Fixes

- Product url access on product pages in js (#87)



## [0.20.0](https://github.com/org/repo/compare/0.19.0...0.20.0) - 2021-11-03

### Added

- GraphQL callback prop (46dcc39)



## [0.19.0](https://github.com/org/repo/compare/0.18.0...0.19.0) - 2021-11-03

### Added

- Notification duration prop (#85)
- Slider reference prop (#86)

### Changed

- Redirect with configurable products and disabled swatches in listings (#84)



## [0.18.0](https://github.com/org/repo/compare/0.17.0...0.18.0) - 2021-10-23

### Added

- Sensitive/encrypted config support (5def207)
- Recaptcha support in the GraphQL mutation component (8020ead)
- Stack in the head of the layout (c0bcbc1)

### Fixed

- Only show the page content heading when filled (bcf3fde)



## [0.17.0](https://github.com/org/repo/compare/0.16.0...0.17.0) - 2021-10-20

### Added

- Add to cart adding/added states and notify props (3d1b012, d367430)



## [0.16.0](https://github.com/org/repo/compare/0.15.2...0.16.0) - 2021-10-08

### Added

- GraphQL component prop to mutate on event (#80)

### Changed

- Kebab-case events names (#81)



## [0.15.2](https://github.com/org/repo/compare/0.15.1...0.15.2) - 2021-10-07

### Changed

- Allow post requests to the admin routes (6e91d62)

### Fixed

- Slider totals fix (#78)
- Spelling fix (#79)



## [0.15.1](https://github.com/org/repo/compare/0.15.0...0.15.1) - 2021-10-07

### Fixed

- GraphQL Mutation component reactivity fix (092331a)



## [0.15.0](https://github.com/org/repo/compare/0.14.2...0.15.0) - 2021-10-05

### Added

- Configurable z-indexes (#76)

### Changed

- Keep the email after the checkout and pass variables to GraphQL callbacks (#77)



## [0.14.2](https://github.com/org/repo/compare/0.14.1...0.14.2) - 2021-09-28

### Added

- Added the variables to the GraphQL mutation slot scope (#73)



## [0.14.1](https://github.com/org/repo/compare/0.14.0...0.14.1) - 2021-09-24

### Fixed

- Hide slider dots when there is just one slide (#70)
- Clear cart on success page (#71)



## [0.14.0](https://github.com/org/repo/compare/0.13.0...0.14.0) - 2021-09-22

### Added

- Slider navigation dots (#66)
- GraphQL component variables support (b0a9ed4)

### Changed

- Expose the product id to the frontend (ab28cac)
- Use localstorage email as guest email if available (#69)



## [0.13.0](https://github.com/org/repo/compare/0.12.0...0.13.0) - 2021-09-21

### Added

- Qty increments in the cart (6442c1c, 8036645)
- CheckoutPaymentSaved event order data (#67)

### Changed

- Refactored the button component (dae4152)

### Fixed

- Always refresh the cart after changes (174f473)
- Resolve swatches anywhere (#68)



## [0.12.0](https://github.com/org/repo/compare/0.11.0...0.12.0) - 2021-09-16

### Added

- Variable disable when loading button option (6abaae4)

### Fixed

- Allow Vue to set a href on an anchor button (6ccaefe)
- Select filters do not react on themself and cleanup (bfbe4a7)
- Always get the lowest price as base price (#65)



## [0.11.0](https://github.com/org/repo/compare/0.10.1...0.11.0) - 2021-09-10

### Added

- Aria labels (b410c22)
- Width/height attributes on images (5f46d7a)

### Changed

- Bigger arrow icons on the image carousel (2db0bf9)

### Fixed

- Unique ids (cc3d9ac)

### Security

- Updated lodash (60a68bf)



## [0.10.1](https://github.com/org/repo/compare/0.10.0...0.10.1) - 2021-09-07

### Fixed

- Fixed overwriting select (#62)



## [0.10.0](https://github.com/org/repo/compare/0.9.3...0.10.0) - 2021-09-01

### Added

- Widget replace option (9b986bc)



## [0.9.3](https://github.com/org/repo/compare/0.9.2...0.9.3) - 2021-09-01

### Changed

- Refresh cart totals when selecting a payment method (d4d8aaf, 8369ff0)



## [0.9.2](https://github.com/org/repo/compare/0.9.1...0.9.2) - 2021-08-31

### Changed

- Test against Magento 2.4.2-p2 and 2.4.3 (465d430)
- Use title from options for the productlist widget (276d739, 2fd56eb)
- Use the button component for the slider buttons (9bb54e7)



## [0.9.1](https://github.com/org/repo/compare/0.9.0...0.9.1) - 2021-08-27

### Fixed

- Productlist slider button position (48a3d15)



## [0.9.0](https://github.com/org/repo/compare/0.8.0...0.9.0) - 2021-08-27

### Added

- Productlist slider (96f3523, 8ea1d29)



## [0.8.0](https://github.com/org/repo/compare/0.7.0...0.8.0) - 2021-08-27

### Added

- Toggler component open prop (#59)
- Canonical yield (7938a6c)
- Eventy global scopes filter on all models (#60)

### Fixed

- HasEventyGlobalScopeFilter trait boot method name (#61)
- Productlist widget bugfix (269d886)



## [0.7.0](https://github.com/org/repo/compare/0.6.1...0.7.0) - 2021-08-24

### Added

- Configurable widgets (#57)
- Content directive (edbf945)



## [0.6.1](https://github.com/org/repo/compare/0.6.0...0.6.1) - 2021-08-17

### Fixed

- Product image thumbnail styling (2a34c4c)



## [0.6.0](https://github.com/org/repo/compare/0.5.0...0.6.0) - 2021-08-17

### Added

- Product images carousel (91e4997)



## [0.5.0](https://github.com/org/repo/compare/0.4.0...0.5.0) - 2021-08-13

### Added

- Breadcrumbs on products by latest category (647334e)



## [0.4.0](https://github.com/org/repo/compare/0.3.2...0.4.0) - 2021-08-13

### Added

- Breadcrumbs (5db2a36)

### Fixed

- Prevent Vue warning (eb19308)



## [0.3.2](https://github.com/org/repo/compare/0.3.1...0.3.2) - 2021-08-12

### Changed

- Overwritable default qty and use the qty_increments (7ac384c)



## [0.3.1](https://github.com/org/repo/compare/0.3.0...0.3.1) - 2021-08-12

### Changed

- Possibility to use the refresh cart method without Vue (e37312e)
- Pass through the Axios response with the GraphQL Mutation callback (3564519)



## [0.3.0](https://github.com/org/repo/compare/0.2.2...0.3.0) - 2021-08-10

### Added

- Product quantity increments (cf21993)



## [0.2.2](https://github.com/org/repo/compare/0.2.1...0.2.2) - 2021-08-07

### Fixed

- Typo (1a4de04)



## [0.2.1](https://github.com/org/repo/compare/0.2.0...0.2.1) - 2021-08-07

### Fixed

- Only show discount when it is not 0 (1621edb)



## [0.2.0](https://github.com/org/repo/compare/0.1.4...0.2.0) - 2021-08-03

### Added

- Configurable models (#56)



## [0.1.4](https://github.com/org/repo/compare/0.1.3...0.1.4) - 2021-07-20

### Changed

- Dynamic IsActiveScope column (4e97b21)



## [0.1.3](https://github.com/org/repo/compare/0.1.2...0.1.3) - 2021-07-19

### Changed

- GraphQL mutation component callback prop (446d3c6)
- Possiblity to use some methods without Vue (51d6b6b)



## [0.1.2](https://github.com/org/repo/compare/0.1.1...0.1.2) - 2021-07-08

### Fixed

- Image position fix (#52)



## [0.1.1](https://github.com/org/repo/compare/0.1.0...0.1.1) - 2021-07-07

### Added
- WebP images from rapidez/image-resizer with fallback (#51)



## [0.1.0](https://github.com/org/repo/compare/a25327b8600496e5d5e060609fa787379639252e...0.1.0) - 2021-06-23

Public beta release!




