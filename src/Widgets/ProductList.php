<?php

namespace Rapidez\Core\Widgets;

use Rapidez\Core\Facades\Rapidez;

class ProductList
{
    protected mixed $condition;
    protected mixed $options;

    public function __construct(mixed $options)
    {
        // @phpstan-ignore-next-line
        $conditions = collect($options->conditions ?? Rapidez::fancyMagentoSyntaxDecoder($options->conditions_encoded));
        $this->condition = $conditions->first(function (mixed $condition) {
            return $condition->type == 'Magento\CatalogWidget\Model\Rule\Condition\Product';
        });
        $this->options = $options;
    }

    public function render(): string
    {
        return view('rapidez::widget.productlist', [
            'options'   => $this->options,
            'condition' => $this->condition,
        ])->render();
    }
}
