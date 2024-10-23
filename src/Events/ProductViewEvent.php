<?php

namespace Rapidez\Core\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Rapidez\Core\Models\Product;

class ProductViewEvent
{
    use Dispatchable;
    use SerializesModels;

    public Product $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }
}
