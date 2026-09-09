<?php

namespace Rapidez\Core\Listeners;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Rapidez\Core\Actions\GetLatestIndexTimestamp;
use Rapidez\Core\Events\IndexAfterEvent;

class UpdateLatestIndexDate
{
    public function handle()
    {
        return Storage::disk('local')->put(
            '/.last-index',
            resolve(GetLatestIndexTimestamp::class)->get(),
        );
    }

    public static function register()
    {
        Event::listen(IndexAfterEvent::class, static::class);
    }
}
