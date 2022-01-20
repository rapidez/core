<?php

namespace Rapidez\Core\Http\Controllers;

class CategoryController
{
    public function show(int $categoryId)
    {
        $categoryModel = config('rapidez.models.category');
        $category = $categoryModel::findOrFail($categoryId);

        config(['frontend.category' => $category->only(['entity_id', 'pathWithNames'])]);
        session(['latest_category_path' => $category->path]);

        return view('rapidez::category.overview', compact('category'));
    }
}
