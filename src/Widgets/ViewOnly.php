<?php

namespace Rapidez\Core\Widgets;

class ViewOnly
{
    protected mixed $options;

    public function __construct(mixed $options)
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
