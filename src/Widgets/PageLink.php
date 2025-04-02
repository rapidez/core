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
        if ($pageContent = Page::withoutGlobalScope(IsActiveScope::class)->find($vars->page_id)) {
            $this->identifier = $pageContent['identifier'];
            $this->anchorText = $vars->anchor_text ?? $pageContent['title'] ?? 'link';
            $this->title = $vars->title ?? $pageContent['title'] ?? 'title';
        } else {
            $this->identifier = '';
            $this->anchorText = $vars->anchor_text  ?? 'link';
            $this->title = $vars->title ?? 'title';
        }
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
