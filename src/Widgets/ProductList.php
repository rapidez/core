<?php

namespace Rapidez\Core\Widgets;

use Illuminate\Support\Collection;
use Rapidez\Core\Rapidez;

class ProductList
{
    protected Collection $conditions;
    protected $condition;
    protected Collection $options;

    public function __construct($options)
    {
        $this->conditions = collect(Rapidez::fancyMagentoSyntaxDecoder($options['conditions_encoded']));
        $this->condition = $this->conditions->first(function ($condition) {
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
