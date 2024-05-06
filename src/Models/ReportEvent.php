<?php

namespace Rapidez\Core\Models;

use Rapidez\Core\Models\Model;

class ReportEvent extends Model
{
    protected $table = 'report_event';

    protected $primaryKey = 'event_id';

    protected $guarded = ['event_id'];

    const CREATED_AT = 'logged_at';

    const UPDATED_AT = null;
}
