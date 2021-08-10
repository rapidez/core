<?php

namespace Rapidez\Core\ViewDirectives;

use Rapidez\Core\Models\Traits\HasContentAttributeWithVariables;
use Rapidez\Core\Rapidez;

class ContentDirective
{
    use HasContentAttributeWithVariables;

    public function render($content)
    {
        return (new Rapidez())->getContent($content);
    }
}
