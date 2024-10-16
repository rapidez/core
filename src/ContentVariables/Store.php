<?php

namespace Rapidez\Core\ContentVariables;

class Store
{
    /** @return array<int, string>|string|null */
    public function __invoke(string $content): array|string|null
    {
        return preg_replace('/{{store (url|direct_url)=("|&quot;|\')(.*?)("|&quot;|\')}}/m', '/${3}', $content);
    }
}
