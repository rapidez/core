<?php

namespace Rapidez\Core\ContentVariables;

class Media
{
    /** @return array<int, string>|string|null */
    public function __invoke(string $content): array|string|null
    {
        return preg_replace('/{{media url=("|&quot;|\')(.*?)("|&quot;|\')}}/m', config('rapidez.media_url') . '/${2}', $content);
    }
}
