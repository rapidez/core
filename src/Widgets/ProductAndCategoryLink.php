<?php

namespace Rapidez\Core\Widgets;

class ProductAndCategoryLink
{
    protected $idPath;

    protected $title;

    protected $anchorText;

    public function __construct($vars)
    {
        $this->idPath = match (str($vars->id_path)->before('/')->value()) {
            'category' => str($vars->id_path ?? '')->after('/')->prepend('catalog/category/view/id/'),
            'product' => str($vars->id_path ?? '')->after('/')->prepend('catalog/product/view/id/'),
            default => 'unknown'
        };
        $this->title = $vars->title ?? 'title';
        $this->anchorText = $vars->anchor_text ?? 'link';
    }

    public function render()
    {
        return view('rapidez::widget.link', [
            'title' => $this->title,
            'url' => $this->idPath,
            'anchorText' => $this->anchorText,
        ]);
    }
}
