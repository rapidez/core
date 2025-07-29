<?php

namespace Rapidez\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallTestsCommand extends Command
{
    protected $signature = 'rapidez:install:tests';

    protected $description = 'Install Rapidez Tests';

    public function handle()
    {
        File::copyDirectory(
            base_path('vendor/rapidez/core/tests/playwright'),
            base_path('tests/playwright')
        );

        File::copy(
            base_path('vendor/rapidez/core/playwright.config.js'),
            base_path('playwright.config.js')
        );

        $this->info('Done 🚀 you can now run the browser tests with: yarn playwright test');
    }
}
