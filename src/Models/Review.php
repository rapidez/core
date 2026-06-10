<?php

namespace Rapidez\Core\Models;

class Review extends Model
{
    protected $table = 'review_detail';

    protected $primaryKey = 'detail_id';

    // TODO: RAP-1880 Add rating values to reviews (this is currently unused in favor of Graphql)
}
