<?php

namespace Rapidez\Core\Http\Controllers;

use Illuminate\Routing\Controller;

class CategoryController extends Controller
{
    public function show(int $categoryId)
    {
        $categoryModel = config('rapidez.models.category');
        $category = $categoryModel::findOrFail($categoryId);

        config(['frontend.category' => $category->only('entity_id')]);
        config(['frontend.subcategories' => $category->subcategories->pluck('name', 'entity_id')]);
        session(['latest_category_path' => $category->path]);

        $response = response()->view('rapidez::category.overview', compact('category'));
        $response->setCache(['etag' => md5($response->getContent() ?? ''), 'last_modified' => $category->updated_at]);

        return $response;
    }
}
