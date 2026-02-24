<?php

namespace Rapidez\Core\Events;

use Illuminate\Foundation\Events\Dispatchable;

class IndexStoreBeforeEvent
{
    use Dispatchable;

    public $context;
    public int $store;

    public function __construct($context, int $store)
    {
        $this->context = $context;
        $this->store = $store;
    }
}
