<?php

namespace Rapidez\Core\Widgets;

class ProductAndCategoryLink
{
    protected $url;

    protected $title;

    protected $anchorText;

    public function __construct($vars)
    {
        $type = str($vars->id_path)->before('/')->value();
        $id = str($vars->id_path)->after('/');

        $this->url = match ($type) {
            'category' => $id->prepend('catalog/category/view/id/'),
            'product' => $id->prepend('catalog/product/view/id/'),
        };

        $this->title = $vars->title ?? '';
        $this->anchorText = $vars->anchor_text ?? 'link';
    }

    public function render()
    {
        return view('rapidez::widget.link', [
            'title' => $this->title,
            'url' => $this->url,
            'anchorText' => $this->anchorText,
        ]);
    }
}
