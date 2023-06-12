<?php

namespace Rapidez\Core\ContentVariables;

class Media
{
    public function __invoke($content)
    {
        return preg_replace('/{{media url=("|&quot;|\')(.*?)("|&quot;|\')}}/m', config('rapidez.media_url').'/${2}', $content);
    }
}
