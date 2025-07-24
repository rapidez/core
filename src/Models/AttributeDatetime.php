<?php

namespace Rapidez\Core\Models;

class AttributeDatetime extends AbstractAttribute
{
    protected $casts = ['value' => 'datetime'];
}
