<?php

namespace Rapidez\Core\Models;

class AttributeDateTime extends EavAttribute
{
    protected $casts = ['value' => 'datetime'];
}
