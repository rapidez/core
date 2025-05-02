<?php

namespace Rapidez\Core\Actions;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Translation\Translator;

class CollectFrontendTranslations
{
    public static function collect(): array
    {
        /** @var Translator $translator */
        $translator = app(Translator::class);
        $loader = $translator->getLoader();

        $allTranslations = [];

        $allTranslations = collect($loader->namespaces())
            ->filter(function (string $path, string $namespace): bool {
                return str($namespace)->startsWith('rapidez');
            })->map(function (string $path, string $namespace): array {
                $file = $path . '/' . App::getLocale() . '/frontend.php';

                if (File::exists($file)) {
                    $translations = collect(File::getRequire($file));

                    if ($namespace !== 'rapidez') {
                        return [
                            'packages' => [
                                str($namespace)->after('rapidez-')->toString() => $translations->toArray(),
                            ],
                        ];
                    }

                    return $translations->toArray();
                }

                return [];
            })
            ->filter()
            ->reduce(function (array $allTranslations, array $translations): array {
                return array_merge_recursive($allTranslations, $translations);
            }, []);

        return $allTranslations;
    }
}
