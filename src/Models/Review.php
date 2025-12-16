<?php

namespace Rapidez\Core\Models;

class Review extends Model
{
    public const REVIEW_ENTITY_PRODUCT = 1;
    public const REVIEW_ENTITY_CUSTOMER = 2;
    public const REVIEW_ENTITY_CATEGORY = 3;

    protected $table = 'review_detail';

    protected $primaryKey = 'detail_id';

    // TODO: Add rating values to reviews
}
