<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\SoftDeleteHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SoftDeleteController extends Controller
{
    /**
     * Display deleted categories and products.
     *
     * Features:
     * - Deleted record search
     * - Direct/Cascade source filter
     * - Pagination
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $source = $request->input('source');

        $categoryPerPage = (int) $request->input(
            'category_per_page',
            5
        );

        $productPerPage = (int) $request->input(
            'product_per_page',
            5
        );

        if (!in_array($categoryPerPage, [5, 10, 25, 50], true)) {
            $categoryPerPage = 5;
        }

        if (!in_array($productPerPage, [5, 10, 25, 50], true)) {
            $productPerPage =5;
        }

        /*
         * 8. Deleted category search
         */
        $categoryQuery = Category::onlyTrashed()
            ->with([
                'products' => function ($query) {
                    $query->withTrashed();
                }
            ]);

        if ($search !== '') {
            $categoryQuery->where(
                'name',
                'like',
                '%' . $search . '%'
            );
        }

        $deletedCategories = $categoryQuery
            ->oldest('deleted_at')
            ->paginate(
                $categoryPerPage,
                ['*'],
                'categories_page'
            )
            ->withQueryString();

        /*
         * Deleted product query.
         */
        $productQuery = Product::onlyTrashed()
            ->with([
                'category' => function ($query) {
                    $query->withTrashed();
                }
            ]);

        if ($search !== '') {
            $productQuery->where(
                'name',
                'like',
                '%' . $search . '%'
            );
        }

        /*
         * 9. Deleted product source filter
         *
         * direct = directly deleted product
         * cascade = deleted because category was deleted
         */
        if (in_array($source, ['direct', 'cascade'], true)) {

            $productIds = SoftDeleteHistory::where(
                'entity_type',
                'product'
            )
                ->where('deletion_source', $source)
                ->pluck('entity_id');

            $productQuery->whereIn('id', $productIds);
        }

        $deletedProducts = $productQuery
            ->oldest('deleted_at')
            ->paginate(
                $productPerPage,
                ['*'],
                'products_page'
            )
            ->withQueryString();

        return view(
            'soft-deletes.index',
            compact(
                'deletedCategories',
                'deletedProducts',
                'search',
                'source',
                'categoryPerPage',
                'productPerPage'
            )
        );
    }

    /**
     * Restore a deleted category and its cascade-deleted products.
     */
    public function restoreCategory($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);

        DB::transaction(function () use ($category) {

            $category->restore();

            $cascadeProductIds = SoftDeleteHistory::where(
                'entity_type',
                'product'
            )
                ->where('parent_type', 'category')
                ->where('parent_id', $category->id)
                ->where('deletion_source', 'cascade')
                ->where('action', 'deleted')
                ->pluck('entity_id');

            if ($cascadeProductIds->isNotEmpty()) {

                Product::onlyTrashed()
                    ->whereIn('id', $cascadeProductIds)
                    ->restore();

                SoftDeleteHistory::where(
                    'entity_type',
                    'product'
                )
                    ->whereIn('entity_id', $cascadeProductIds)
                    ->where('action', 'deleted')
                    ->where('deletion_source', 'cascade')
                    ->update([
                        'action' => 'restored',
                        'event_at' => now(),
                    ]);
            }

            SoftDeleteHistory::where(
                'entity_type',
                'category'
            )
                ->where('entity_id', $category->id)
                ->where('action', 'deleted')
                ->oldest()
                ->first()
                ?->update([
                    'action' => 'restored',
                    'event_at' => now(),
                ]);
        });

        return back()
            ->with(
                'success',
                '♻️ Category Restored Successfully — Cascade Deleted Products Restored'
            );
    }

    /**
     * Restore one deleted product.
     */
    public function restoreProduct($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);

        $category = Category::withTrashed()
            ->find($product->category_id);

        if (!$category) {
            return back()->with(
                'error',
                '❌ Product cannot be restored because its category no longer exists.'
            );
        }

        if ($category->trashed()) {
            return back()->with(
                'error',
                '⚠️ Restore the deleted category first, then restore this product.'
            );
        }

        DB::transaction(function () use ($product) {

            $product->restore();

            SoftDeleteHistory::where(
                'entity_type',
                'product'
            )
                ->where('entity_id', $product->id)
                ->where('action', 'deleted')
                ->oldest()
                ->first()
                ?->update([
                    'action' => 'restored',
                    'event_at' => now(),
                ]);
        });

        return back()
            ->with('success', '♻️ Product Restored Successfully');
    }

    /**
     * Permanently delete a category.
     */
    public function forceDeleteCategory($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);

        DB::transaction(function () use ($category) {

            Product::withTrashed()
                ->where('category_id', $category->id)
                ->forceDelete();

            $category->forceDelete();

            SoftDeleteHistory::where(function ($query) use ($category) {

                $query
                    ->where(function ($q) use ($category) {
                        $q->where('entity_type', 'category')
                            ->where(
                                'entity_id',
                                $category->id
                            );
                    })
                    ->orWhere(function ($q) use ($category) {
                        $q->where('entity_type', 'product')
                            ->where(
                                'parent_type',
                                'category'
                            )
                            ->where(
                                'parent_id',
                                $category->id
                            );
                    });

            })->delete();
        });

        return back()
            ->with(
                'success',
                '🔥 Category and Its Deleted Products Permanently Removed'
            );
    }

    /**
     * Permanently delete a product.
     */
    public function forceDeleteProduct($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);

        DB::transaction(function () use ($product) {

            $product->forceDelete();

            SoftDeleteHistory::where(
                'entity_type',
                'product'
            )
                ->where('entity_id', $product->id)
                ->delete();
        });

        return back()
            ->with(
                'success',
                '🔥 Product Permanently Deleted'
            );
    }

    /**
     * Display soft delete analytics and history.
     *
     * Feature 10:
     * Analytics action/source filtering.
     */
    public function analytics(Request $request)
    {
        $action = $request->input('action');
        $source = $request->input('source');

        $activeCategories = Category::count();

        $deletedCategories = Category::onlyTrashed()->count();

        $totalCategories =
            $activeCategories + $deletedCategories;

        $activeProducts = Product::count();

        $deletedProducts = Product::onlyTrashed()->count();

        $totalProducts =
            $activeProducts + $deletedProducts;

        $cascadeDeletedProducts = SoftDeleteHistory::where(
            'entity_type',
            'product'
        )
            ->where('deletion_source', 'cascade')
            ->where('action', 'deleted')
            ->count();

        $directDeletedProducts = SoftDeleteHistory::where(
            'entity_type',
            'product'
        )
            ->where('deletion_source', 'direct')
            ->where('action', 'deleted')
            ->count();

        $restoredCategories = SoftDeleteHistory::where(
            'entity_type',
            'category'
        )
            ->where('action', 'restored')
            ->count();

        $restoredProducts = SoftDeleteHistory::where(
            'entity_type',
            'product'
        )
            ->where('action', 'restored')
            ->count();

        /*
         * Analytics history filters.
         */
        $historyQuery = SoftDeleteHistory::query();

        if (in_array($action, ['deleted', 'restored'], true)) {
            $historyQuery->where('action', $action);
        }

        if (in_array($source, ['direct', 'cascade'], true)) {
            $historyQuery->where(
                'deletion_source',
                $source
            );
        }

        $recentHistory = $historyQuery
            ->oldest('event_at')
            ->limit(5)
            ->get();

        return view(
            'soft-deletes.analytics',
            compact(
                'activeCategories',
                'deletedCategories',
                'totalCategories',
                'activeProducts',
                'deletedProducts',
                'totalProducts',
                'cascadeDeletedProducts',
                'directDeletedProducts',
                'restoredCategories',
                'restoredProducts',
                'recentHistory',
                'action',
                'source'
            )
        );
    }
}