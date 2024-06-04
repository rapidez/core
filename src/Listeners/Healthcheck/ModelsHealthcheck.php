<?php

namespace Rapidez\Core\Listeners\Healthcheck;

use Rapidez\Core\Models\Model;

class ModelsHealthcheck extends Base
{
    public function handle()
    {
        $response = [
            'healthy'  => true,
            'messages' => [],
        ];
        $classesWithIncorrectParent = collect(config('rapidez.models'))->filter(fn ($model) => ! is_subclass_of($model, Model::class));

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
