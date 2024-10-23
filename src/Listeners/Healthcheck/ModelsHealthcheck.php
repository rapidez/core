<?php

namespace Rapidez\Core\Listeners\Healthcheck;

use Rapidez\Core\Models\Model;

class ModelsHealthcheck extends Base
{
    /** @return array<string, mixed> */
    public function handle(): array
    {
        $response = [
            'healthy'  => true,
            'messages' => [],
        ];

        /** @var array<string, string> $models */
        $models = config('rapidez.models');

        /** @var \Illuminate\Support\Collection<string, string> $classesWithIncorrectParent */
        $classesWithIncorrectParent = collect($models)->filter(fn ($model) => ! is_subclass_of($model, Model::class));

        if (! $classesWithIncorrectParent->count()) {
            return $response;
        }

        $response['messages'][] = [
            'type'  => 'warn',
            'value' => __('Models should extend :rapidezModel, the following dont: :models', ['rapidezModel' => Model::class, 'models' => PHP_EOL . '- ' . $classesWithIncorrectParent->implode(PHP_EOL . '- ')]),
        ];

        return $response;
    }
}
