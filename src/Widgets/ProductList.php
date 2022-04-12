<?php

namespace Rapidez\Core\Widgets;

use Rapidez\Core\Facades\Rapidez;

class ProductList
{
    protected $condition;
    protected object $options;

    public function __construct($options)
    {
        $conditions = collect($options->conditions ?? Rapidez::fancyMagentoSyntaxDecoder($options->conditions_encoded));
        $this->condition = $conditions->first(function ($condition) {
            return $condition->type == 'Magento\CatalogWidget\Model\Rule\Condition\Product';
        });
        $this->options = $options;
    }

    public function render()
    {
        return view('rapidez::widget.productlist', [
            'options'   => $this->options,
            'condition' => $this->condition,
        ])->render();
    }
}
