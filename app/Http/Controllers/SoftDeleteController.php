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
     */
    public function index()
    {
        $deletedCategories = Category::onlyTrashed()
            ->with([
                'products' => function ($query) {
                    $query->withTrashed();
                }
            ])
            ->latest('deleted_at')
            ->get();

        $deletedProducts = Product::onlyTrashed()
            ->with([
                'category' => function ($query) {
                    $query->withTrashed();
                }
            ])
            ->latest('deleted_at')
            ->get();

        return view(
            'soft-deletes.index',
            compact(
                'deletedCategories',
                'deletedProducts'
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

            /*
             * Restore category.
             */
            $category->restore();

            /*
             * Find products that were deleted because
             * of this category's cascade delete.
             */
            $cascadeProductIds = SoftDeleteHistory::where(
                'entity_type',
                'product'
            )
                ->where(
                    'parent_type',
                    'category'
                )
                ->where(
                    'parent_id',
                    $category->id
                )
                ->where(
                    'deletion_source',
                    'cascade'
                )
                ->pluck('entity_id');

            /*
             * Restore only the products that were part
             * of the cascade deletion.
             */
            if ($cascadeProductIds->isNotEmpty()) {

                Product::onlyTrashed()
                    ->whereIn('id', $cascadeProductIds)
                    ->restore();

                SoftDeleteHistory::where('entity_type', 'product')
                    ->whereIn('entity_id', $cascadeProductIds)
                    ->where('action', 'deleted')
                    ->where('deletion_source', 'cascade')
                    ->update([
                        'action' => 'restored',
                        'event_at' => now(),
                    ]);
            }

            /*
             * Mark category history as restored.
             */
            SoftDeleteHistory::where('entity_type', 'category')
                ->where('entity_id', $category->id)
                ->where('action', 'deleted')
                ->latest()
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

        /*
         * Do not restore a product if its category
         * is still deleted.
         */
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

            SoftDeleteHistory::where('entity_type', 'product')
                ->where('entity_id', $product->id)
                ->where('action', 'deleted')
                ->latest()
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

            /*
             * Permanently remove cascade-deleted products.
             */
            Product::withTrashed()
                ->where('category_id', $category->id)
                ->forceDelete();

            /*
             * Permanently remove category.
             */
            $category->forceDelete();

            /*
             * Remove related history.
             */
            SoftDeleteHistory::where(function ($query) use ($category) {
                $query
                    ->where(function ($q) use ($category) {
                        $q->where('entity_type', 'category')
                            ->where('entity_id', $category->id);
                    })
                    ->orWhere(function ($q) use ($category) {
                        $q->where('entity_type', 'product')
                            ->where('parent_type', 'category')
                            ->where('parent_id', $category->id);
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

            SoftDeleteHistory::where('entity_type', 'product')
                ->where('entity_id', $product->id)
                ->delete();
        });

        return back()
            ->with('success', '🔥 Product Permanently Deleted');
    }

    /**
     * Display soft delete analytics and history.
     */
    public function analytics()
    {
        $activeCategories = Category::count();

        $deletedCategories = Category::onlyTrashed()->count();

        $totalCategories = $activeCategories + $deletedCategories;

        $activeProducts = Product::count();

        $deletedProducts = Product::onlyTrashed()->count();

        $totalProducts = $activeProducts + $deletedProducts;

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

        $recentHistory = SoftDeleteHistory::latest('event_at')
            ->limit(15)
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
                'recentHistory'
            )
        );
    }
}