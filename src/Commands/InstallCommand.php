<?php

namespace Rapidez\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\pause;
use function Laravel\Prompts\select;

class InstallCommand extends Command
{
    protected $signature = 'rapidez:install {--frontendonly : Just copy over the frontend files}';

    protected $description = 'Install Rapidez';

    protected Collection $selectedPackages;

    protected bool $dockerInstall = false;

    public function handle()
    {
        if ($this->option('frontendonly')) {
            $this->frontend(true);

            return;
        }

        $this
            ->intro()
            ->magento()
            ->packages()
            ->validate()
            ->frontend()
            ->finalize();
    }

    protected function intro()
    {
        $this->info(str_repeat('=', 37));
        $this->info('  ____             _     _          ');
        $this->info(" |  _ \ __ _ _ __ (_) __| | ___ ____");
        $this->info(" | |_) / _` | '_ \| |/ _` |/ _ \_  /");
        $this->info(' |  _ < (_| | |_) | | (_| |  __// / ');
        $this->info(" |_| \_\__,_| .__/|_|\__,_|\___/___|");
        $this->info('            |_|                     ');
        $this->info('  Welcome to the Rapidez installer!');
        $this->info(str_repeat('=', 37));
        $this->newLine();
        $this->warn('> You will be given some prompts to customize your Rapidez installation.');
        $this->warn('> If you\'re unsure, stick to the given default options.');
        $this->newLine(2);

        return $this;
    }

    protected function magento()
    {
        if (confirm('Do you have a Magento installation up-and-running?')) {
            return $this;
        }

        if (! confirm(
            label: 'Shall we setup a demo Magento installation for you in Docker?',
            hint: 'This will run `docker compose up -d` and takes a while, make sure Docker is running!'
        )) {
            $this->error('Please setup a Magento installation first!');
            exit;
        }

        $this->dockerInstall = true;

        if (passthru('docker compose up -d') === false) {
            $this->newLine();
            $this->error('Something went wrong, please check the errors and try again');
            exit;
        }

        if (passthru('docker exec rapidez_magento magerun2 indexer:reindex') === false) {
            $this->newLine();
            $this->error('Something went wrong, please check the errors and try again');
            exit;
        }

        return $this;
    }

    protected function validate()
    {
        if ($this->dockerInstall || confirm('Did you configure the Magento credentials in the .env?')) {
            $this->info('We are going to run `php artisan rapidez:validate` now');
            if ($this->call('rapidez:validate')) {
                $this->newLine();
                $this->error('The validation did not pass, please fix the errors first');
                exit;
            }
        } else {
            $this->error('Please add your Magento credentials the the .env first');
            exit;
        }

        return $this;
    }

    protected function frontend($force = false)
    {
        $filesToCopy = [
            'package.json',
            'postcss.config.js',
            'tailwind.config.js',
            'vite.config.js',
            'yarn.lock',
            '.prettierrc.js',
        ];

        if ($force || confirm(
            label: 'Copy all files for the frontend dependencies?',
            hint: 'The files that will be copied to your project: ' . implode(', ', $filesToCopy)
        )) {
            foreach ($filesToCopy as $file) {
                copy(__DIR__ . '/../../' . $file, base_path($file));
            }
        }

        if ($force) {
            return $this;
        }

        if (confirm(
            label: 'Install and build the frontend dependencies?',
            hint: 'This will run `yarn` and `yarn run prod`'
        )) {
            passthru('yarn');

            if ($this->selectedPackages->contains('rapidez/sentry')) {
                $this->line('Sentry also needs @sentry/vue');
                passthru('yarn add @sentry/vue -D');
            }

            if ($this->selectedPackages->contains('rapidez/openreplay')) {
                $this->line('Openreplay also needs @openreplay/tracker');
                passthru('yarn add @openreplay/tracker -D');
            }

            passthru('yarn run prod');
        }

        return $this;
    }

    protected function packages()
    {
        $this->warn('We are going to ask you which packages you would like to install,');
        $this->warn('at the end we will show your selection before we do anything.');
        $this->warn('Made a mistake? You can change it later!');

        pause('Press ENTER to start!');

        $packages[] = select(
            label: 'Shall we install a CMS integration?',
            options: [
                null       => 'No thanks',
                'statamic' => 'Statamic',
                'strapi'   => 'Strapi',
            ],
        );

        $packages[] = multiselect(
            label: 'Shall we install a monitoring integration?',
            options: [
                'sentry'     => 'Sentry',
                'openreplay' => 'Openreplay',
            ],
        );

        $packages[] = multiselect(
            label: 'Would you like any of these optional functionalities?',
            options: [
                'reviews'           => 'Reviews',
                'compare'           => 'Product compare',
                'login-as-customer' => 'Login as customer possibility for admins',
                'product-alert'     => 'Product alerts, email when a product comes back in stock',
            ],
        );

        $packages[] = select(
            label: 'Shall we install a wishlist integration?',
            options: [
                null                 => 'No thanks',
                'wishlist'           => 'Default Magento wishlist',
                'guest-wishlist'     => 'Guest wishlist where the list is stored in localStorage',
                'multiple-wishlists' => 'Multiple wishlists, with a custom database table and API endpoints',
            ],
        );

        $packages[] = multiselect(
            label: 'Shall we install a payment provider integration?',
            options: [
                'paynl'        => 'Pay.nl',
                'mollie'       => 'Mollie',
                'riverty'      => 'Riverty',
                'multisafepay' => 'MultiSafepay',
            ],
        );

        $packages[] = select(
            label: 'Shall we install a postcode integration?',
            options: [
                null                   => 'No thanks',
                'postcodeservice'      => 'Postcodeservice.com directly',
                'experius-postcode-nl' => 'Postcode.nl, requires the `experius/module-postcode` Magento module',
            ],
        );

        $packages[] = select(
            label: 'Shall we install an opinionated checkout?',
            options: [
                null             => 'No thanks, the default checkout is fine < easy to customize!',
                'checkout-theme' => 'Checkout theme; styled for B2B',
                'confira'        => 'Confira; based on the checkout theme and styled for B2C',
            ],
        );

        if (confirm('Are you going to use Google Tag Manager?', false)) {
            $packages[] = 'gtm';
        }

        if (confirm('Are you going to use Magento Inventory Management (MSI)?', false)) {
            $packages[] = 'msi';
        }

        $this->selectedPackages = collect($packages)
            ->flatten()
            ->filter()
            ->map(fn ($package) => 'rapidez/' . $package);

        if ($this->selectedPackages->isEmpty()) {
            $this->warn('Ok, no whipped cream with stroopwafel pieces on your ice cream for you!');
            $this->newline(2);

            return $this;
        }

        $this->warn('Please review your selected packages:');
        foreach ($this->selectedPackages as $package) {
            $this->line('- ' . $package);
        }

        if (confirm('Are you sure you want all these packages installed?', false)) {
            $this->line('Okido, here we go... we just `composer require` all of them.');
            passthru('composer require ' . $this->selectedPackages->implode(' '));
        } else {
            $this->line('Ok, we are starting over...');
            $this->packages();
        }

        return $this;
    }

    protected function finalize()
    {
        $this->info('We are going to run `php artisan storage:link` now');
        $this->call('storage:link');

        $this->info('We are going to run `php artisan rapidez:index` now');
        $this->call('rapidez:index');

        $this->info('We are going to publish the config files');
        $this->call('vendor:publish', [
            '--tag'      => 'config',
            '--provider' => 'Rapidez\Core\RapidezServiceProvider',
        ]);

        $this->newLine();
        $this->info(str_repeat('=', 37));
        $this->info('All set!');
        $this->newLine();
        $this->warn('Check the docs @ https://docs.rapidez.io');
        $this->warn('Especially about the configuration and CORS!');
        $this->warn('Questions? Join Slack @ https://rapidez.io/slack');
        $this->newLine();

        if ($this->selectedPackages->isNotEmpty()) {
            $this->warn('As you did select some packages before, check the readme of each one!');
            foreach ($this->selectedPackages as $package) {
                $this->line('- https://github.com/' . $package);
            }
        }

        if ($this->selectedPackages->contains('rapidez/statamic')) {
            $this->newLine();
            $this->warn('Next thing to do manually, run: php artisan rapidez-statamic:install');
        }

        return $this;
    }
}
