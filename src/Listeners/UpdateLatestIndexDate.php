<?php

namespace Rapidez\Core\Listeners;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Rapidez\Core\Events\IndexAfterEvent;

class UpdateLatestIndexDate
{
    public function handle()
    {
        return Storage::disk('local')->put(
            '/.last-index',
            // With this we're just making sure the comparison
            // is done within the same timezone in MySQL.
            DB::selectOne('SELECT NOW() AS `current_time`')->current_time
        );
    }

    public static function register()
    {
        Event::listen(IndexAfterEvent::class, static::class);
    }
}
