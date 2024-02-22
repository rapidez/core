<?php

namespace Rapidez\Core\Jobs;

use Carbon\Carbon;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Event;
use Rapidez\Core\Events\ConfigChangeEvent;

class CheckConfigJob
{
    use Dispatchable;

    public function handle(): void
    {
        config('rapidez.models.config')::withoutGlobalScopes()
            ->where('updated_at', '>', Carbon::now()->subHour())
            ->get()
            ->each(fn ($change) => Event::dispatch(ConfigChangeEvent::class, $change));
    }
}
