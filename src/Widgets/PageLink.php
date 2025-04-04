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
        $page = Page::withoutGlobalScope(IsActiveScope::class)->find($vars->page_id)

        $this->identifier = $page->identifier ?? '/';
        $this->anchorText = $vars->anchor_text ?? $page->title ?? 'link';
        $this->title = $vars->title ?? $page->title ?? '';
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
