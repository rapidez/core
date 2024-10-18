<?php

namespace Rapidez\Core\Widgets;

use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Block as ModelsBlock;

class Block
{
    public string $blockId;

    public function __construct(ModelsBlock|string $vars)
    {
        $this->blockId = is_object($vars) ? $vars->block_id : json_decode($vars)->block_id;
    }

    public function render(): string
    {
        $blockModel = config('rapidez.models.block');

        return Rapidez::content($blockModel::find($this->blockId)->content) ?? '';
    }
}
