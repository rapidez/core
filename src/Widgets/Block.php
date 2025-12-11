<?php

namespace Rapidez\Core\Widgets;

use Rapidez\Core\Facades\Rapidez;

class Block
{
    public string $blockId;

    public function __construct($vars)
    {
        $this->blockId = is_object($vars) ? $vars->block_id : json_decode($vars)->block_id;
    }

    public function render()
    {
        $blockModel = config('rapidez.models.block');
        $block = new $blockModel;

        return Rapidez::content($blockModel::where($block->getQualifiedKeyName(), $this->blockId)->orWhere($block->qualifyColumn('identifier'), $this->blockId)->first()->content);
    }
}
