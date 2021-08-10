<?php

namespace Rapidez\Core\Widgets;

class Block
{
    public String $blockId;

    public function __construct($vars)
    {
        $this->blockId = is_array($vars) ? $vars['block_id'] : json_decode($vars)->block_id;
    }

    public function render()
    {
        $blockModel = config('rapidez.models.block');
        
        return $blockModel::find($this->blockId)->content;
    }
}
