<?php

namespace Rapidez\Core\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Rapidez\Core\Commands\IndexProductsCommand;

class IndexAfterEvent
{
    use Dispatchable;

    public IndexProductsCommand $context;

    public function __construct(IndexProductsCommand $context)
    {
        $this->context = $context;
    }
}
