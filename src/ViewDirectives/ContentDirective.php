<?php

namespace Rapidez\Core\ViewDirectives;

use Rapidez\Core\Rapidez;

class ContentDirective
{
    public function render($content)
    {
        return (new Rapidez())->getContent($content);
    }
}
