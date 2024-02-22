<?php

namespace Rapidez\Core\Events;

use Illuminate\Foundation\Events\Dispatchable;

class ConfigChangeEvent
{
    use Dispatchable;

    public $config;

    public function __construct($config)
    {
        $this->config = $config;
    }
}
