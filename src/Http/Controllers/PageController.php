<?php

namespace Rapidez\Core\Http\Controllers;

use Rapidez\Core\Models\Page;

class PageController
{
    public function show(Page $page)
    {
        $response = response()->view('rapidez::page.overview', compact('page'));
        $response->setCache(['etag' => md5($response->getContent() ?? ''), 'last_modified' => $page->updated_at]);
        $response->isNotModified(request());

        return $response;
    }
}
