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
        // TODO: Remove this file and move it to the install command?
        File::copyDirectory(base_path('vendor/rapidez/core/tests/playwright'), base_path('tests/playwright'));

        // With Playwright we don't need this anymore
        File::copyDirectory(base_path('vendor/rapidez/core/tests/Browser'), base_path('tests/Browser'));
        File::copyDirectory(base_path('vendor/rapidez/core/tests/Feature'), base_path('tests/Feature'));

        foreach (File::files(base_path('tests/Feature')) as $file) {
            file_put_contents($file->getPathname(), str_replace(
                'use Rapidez\Core\Tests\TestCase;',
                'use Tests\TestCase;',
                $file->getContents()
            ));
        }

        shell_exec('cd ' . base_path() . ' && composer require --dev laravel/dusk && php artisan dusk:install && php artisan dusk:chrome-driver --detect');

        $duskTestCaseFile = base_path('tests/DuskTestCase.php');
        file_put_contents($duskTestCaseFile, str(file_get_contents($duskTestCaseFile))
            ->replace('use CreatesApplication;', 'use CreatesApplication, DuskTestCaseSetup;')
            ->replace("use Laravel\Dusk\TestCase as BaseTestCase;\n", "use Laravel\Dusk\TestCase as BaseTestCase;\nuse Rapidez\Core\Tests\DuskTestCaseSetup;\n"));

        foreach (File::files(base_path('tests/Browser')) as $file) {
            file_put_contents($file->getPathname(), str_replace(
                'use Rapidez\Core\Tests\DuskTestCase;',
                'use Tests\DuskTestCase;',
                $file->getContents()
            ));
        }

        $this->info('Done 🚀 you can now run the browser tests with: php artisan dusk');
    }
}
