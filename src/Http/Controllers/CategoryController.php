<?php

namespace Rapidez\Core\Http\Controllers;

class CategoryController
{
    public function show(int $categoryId)
    {
        $categoryModel = config('rapidez.models.category');
        $category = $categoryModel::findOrFail($categoryId);

        config(['frontend.category' => $category->only('entity_id')]);
        config(['frontend.subcategories' => $category->subcategories->pluck('name', 'entity_id')]);
        session(['latest_category_path' => $category->path]);

        return view('rapidez::category.overview', compact('category'));
    }
}
