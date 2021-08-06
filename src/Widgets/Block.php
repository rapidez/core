<?php

namespace Rapidez\Core\Widgets;

class Block
{
    public String $blockId;

    public function __construct($vars)
    {
        $this->blockId = is_array($vars) ? $vars['block_id'] : $vars;
    }

    public function render()
    {
        $blockModel = config('rapidez.models.block');
        return $blockModel::find($this->blockId)->content;
    }
}
