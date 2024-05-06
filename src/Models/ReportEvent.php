<?php

namespace Rapidez\Core\Models;

class ReportEvent extends Model
{
    protected $table = 'report_event';

    protected $primaryKey = 'event_id';

    protected $guarded = [];

    const CREATED_AT = 'logged_at';

    const UPDATED_AT = null;
}
