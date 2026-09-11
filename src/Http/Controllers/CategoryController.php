<?php

namespace Rapidez\Core\Http\Controllers;

use Rapidez\Core\Search\CategoryListingSnapshotStore;

class CategoryController
{
    public function show(int $categoryId)
    {
        $categoryModel = config('rapidez.models.category');
        $category = $categoryModel::findOrFail($categoryId);

        config(['frontend.category' => $category->only('entity_id')]);

        // Some fallback routes (e.g. UrlRewriteController) call this controller
        // directly rather than through the router, so this can't rely on method
        // dependency injection to get here.
        $ssrListing = $category->is_anchor ? app(CategoryListingSnapshotStore::class)->get($category) : null;

        $response = response()->view('rapidez::category.overview', compact('category', 'ssrListing'));

        return $response
            ->setEtag(md5($response->getContent() ?? ''))
            ->setLastModified($category->updated_at);
    }
}
