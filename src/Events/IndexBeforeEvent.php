<?php

namespace Rapidez\Core\Events;

use Illuminate\Foundation\Events\Dispatchable;

class IndexBeforeEvent
{
    use Dispatchable;

    public $context;

    public function __construct($context)
    {
        $this->context = $context;
    }
}
