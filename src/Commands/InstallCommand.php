<?php

namespace Rapidez\Core\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'rapidez:install';

    protected $description = 'Install Rapidez';

    public function handle()
    {
        copy(__DIR__.'/../../webpack.mix.js', base_path('webpack.mix.js'));
        copy(__DIR__.'/../../package.json', base_path('package.json'));
        copy(__DIR__.'/../../yarn.lock', base_path('yarn.lock'));
        copy(__DIR__.'/../../tailwind.config.js', base_path('tailwind.config.js'));

        $this->info('Done 🚀');
    }
}
