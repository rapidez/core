<?php

namespace Rapidez\Core\Widgets;

class CategoryLink
{
    protected $idPath;

    protected $title;

    protected $anchorText;

    public function __construct($vars)
    {
        $this->idPath = str($vars->id_path)->after('/')->prepend('catalog/category/view/id/');
        $this->title = $vars->title;
        $this->anchorText = $vars->anchor_text;

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
