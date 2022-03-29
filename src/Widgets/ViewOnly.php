<?php

namespace Rapidez\Core\Widgets;


class ViewOnly
{
    protected $options;

    public function __construct($options)
    {
        $this->options = $options;
    }

    public function render(string $view): string
    {
        return view($view, [
            'options' => $this->options,
        ])->render();
    }
}
