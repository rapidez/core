<?php

namespace Rapidez\Core\Events;

use Illuminate\Foundation\Events\Dispatchable;

class IndexAfterEvent
{
    use Dispatchable;

    public $context;

    public function __construct($context)
    {
        $this->context = $context;
    }
}
