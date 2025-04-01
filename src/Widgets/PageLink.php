<?php

namespace Rapidez\Core\Widgets;

use Rapidez\Core\Models\Page;
use Rapidez\Core\Models\Scopes\IsActiveScope;

class PageLink
{
    protected $identifier;

    protected $title;

    protected $anchorText;

    public function __construct($vars)
    {
        $this->title = $vars->title;
        $this->anchorText = $vars->anchor_text;
        $pageContent = Page::withoutGlobalScope(IsActiveScope::class)->find($vars->page_id);
        $this->identifier = $pageContent['identifier'];
    }

    public function render()
    {
        return view('rapidez::widget.link', [
            'title' => $this->title,
            'url' => $this->identifier,
            'anchorText' => $this->anchorText,
        ]);
    }
}
