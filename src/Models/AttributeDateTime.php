<?php

namespace Rapidez\Core\Models;

class AttributeDateTime extends AbstractAttribute
{
    protected $casts = ['value' => 'datetime'];
}
