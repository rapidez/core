<?php

namespace Rapidez\Core\Models;

class AttributeDecimal extends AbstractAttribute
{
    protected $casts = ['value' => 'float'];
}
