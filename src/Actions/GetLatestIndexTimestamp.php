<?php

namespace Rapidez\Core\Actions;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GetLatestIndexTimestamp
{
    public function get(): Carbon
    {
        $reindexAllInvalidTime = new Carbon(DB::scalar("SELECT scheduled_at FROM cron_schedule WHERE `job_code` = 'indexer_reindex_all_invalid' AND `status` = 'success' ORDER BY `scheduled_at` DESC LIMIT 1") ?? 0);
        $latestFullIndexTime = new Carbon(DB::scalar("SELECT updated FROM indexer_state WHERE `indexer_id` = 'catalog_product_flat' OR `indexer_id` = 'catalog_category_flat' ORDER BY updated LIMIT 1") ?? 0);

        $latest = $reindexAllInvalidTime > $latestFullIndexTime ? $reindexAllInvalidTime : $latestFullIndexTime;
        if ($latest->timestamp == 0) {
            return null;
        }

        return $latest;
    }
}
