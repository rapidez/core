<?php

namespace Rapidez\Core\Http\Controllers;

use Rapidez\Core\Models\Page;

class PageController
{
    public function show(Page $page)
    {
        $response = response()->view('rapidez::page.overview', compact('page'));

        return $response
            ->setEtag(md5($response->getContent() ?? ''))
            ->setLastModified($page->update_time);
    }
}
