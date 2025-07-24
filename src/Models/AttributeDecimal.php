<?php

namespace Rapidez\Core\Models;

class AttributeDecimal extends EavAttribute
{
    protected $casts = ['value' => 'float'];
}
