<?php

namespace Rapidez\Core\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Rapidez\Core\Events\IndexAfterEvent;
use Rapidez\Core\Events\IndexBeforeEvent;
use Rapidez\Core\Facades\Rapidez;
use Rapidez\Core\Models\Traits\Searchable;

class UpdateIndexCommand extends Command
{
    protected $signature = 'rapidez:index:update {--t|types= : To specify types of models to index, separated by commas} {--s|store= : To specify store IDs from Magento, separated by commas}';

    protected $description = 'Attempt to update changed searchable models into ElasticSearch';

    private ?Carbon $latestIndexDate = null;

    public function handle()
    {
        if (!$this->getLatestIndexDate()) {
            $this->error(__('No latest index date has been found yet, please run php artisan rapidez:index first.'));
            return;
        }

        $baseSearchableModels = collect(config('rapidez.models'))
            ->filter(fn ($class) => in_array(Searchable::class, class_uses_recursive($class)) && (new $class)->getQualifiedUpdatedAtColumn());

        $types = $this->option('types')
            ? $baseSearchableModels->filter(fn ($model) => in_array((new $model)->getIndexName(), explode(',', $this->option('types'))))
            : $baseSearchableModels;

        $stores = $this->option('store')
            ? Rapidez::getStores(explode(',', $this->option('store')))
            : Rapidez::getStores();

        IndexBeforeEvent::dispatch($this);

        foreach ($stores as $store) {
            Rapidez::setStore($store);

            $this->line('Store: ' . $store['name']);

            foreach ($types as $type => $model) {
                $this->updateSearchable($model);
            }
        }

        IndexAfterEvent::dispatch($this);
    }

    protected function updateSearchable(string $model): void
    {
        $model::where(fn($query) => $model::makeAllSearchableUsing($query))
            ->where(
                (new $model)->getQualifiedUpdatedAtColumn(),
                '>=',
                $this->getLatestIndexDate()
            )
            ->searchable();
    }

    protected function getLatestIndexDate(): ?Carbon
    {
        if ($this->latestIndexDate) {
            return $this->latestIndexDate;
        }

        if(! Storage::disk('local')->exists('/.last-index')) {
            return null;
        }

        return $this->latestIndexDate ??= Carbon::parse(Storage::disk('local')->get('/.last-index'));
    }
}
