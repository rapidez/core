<?php

namespace Rapidez\Core\ContentVariables;

class Store
{
    public function __invoke($content)
    {
        return preg_replace('/{{store (url|direct_url)=("|&quot;|\')(.*?)("|&quot;|\')}}/m', '/${3}', $content);
    }
}
