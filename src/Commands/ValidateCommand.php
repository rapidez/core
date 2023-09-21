<?php

namespace Rapidez\Core\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

class ValidateCommand extends Command
{
    protected $signature = 'rapidez:validate';

    protected $description = 'Validates all settings';

    public function handle()
    {
        $this->call('cache:clear');
        $result = array_merge_recursive(...Event::dispatch('rapidez:health-check'));
        $isHealthy = Arr::first($result['healthy'], fn ($healthy) => ! $healthy, true);

        foreach ($result['messages'] as $message) {
            (match ($message['type']) {
                'error' => $this->error(...),
                'warn'  => $this->warn(...),
                default => $this->info(...),
            })($message['value']);
        }

        return (int) ! $isHealthy;
    }
}
