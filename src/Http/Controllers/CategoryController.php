<?php

namespace Rapidez\Core\Http\Controllers;

class CategoryController
{
    public function show(int $categoryId)
    {
        $categoryModel = config('rapidez.models.category');
        $category = $categoryModel::findOrFail($categoryId);

        config(['frontend.category' => $category->only('entity_id')]);
        session(['latest_category_path' => $category->path]);

        $response = response()->view('rapidez::category.overview', compact('category'));

        return $response
            ->setEtag(md5($response->getContent() ?? ''))
            ->setLastModified($category->updated_at);
    }
}
