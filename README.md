# Rapidez Core

See: https://github.com/rapidez/rapidez

## Running the tests

### Unit and feature tests

Just run `./vendor/bin/phpunit`

### Browser tests

-   Make sure you've got a Magento install running
-   Prepare Laravel Dusk with `composer run dusk:prepare`
-   Compile the assets with: `composer run dusk:assets` and re-run this when the assets are changed
-   Tests can be started with: `composer run dusk:test`

## License

GNU General Public License v3. Please see [License File](LICENSE) for more information.
