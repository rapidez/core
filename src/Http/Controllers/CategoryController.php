<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Contracts\View\View;

class CategoryController
{
    public function show(int $categoryId): View
    {
        $categoryModel = config('rapidez.models.category');
        $category = $categoryModel::findOrFail($categoryId);

        config(['frontend.category' => $category->only('entity_id')]);
        session(['latest_category_path' => $category->path]);

        return view('rapidez::category.overview', compact('category'));
    }
}
