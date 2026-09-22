<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SoftDeleteHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display all active categories with product count.
     */
    public function index()
    {
        $categories = Category::withCount('products')
            ->latest()
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show category create form.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store new category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        Category::create($validated);

        return redirect()
            ->route('categories.index')
            ->with('success', '✅ Category Created Successfully');
    }

    /**
     * Show edit form.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update category.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        $category->update($validated);

        return redirect()
            ->route('categories.index')
            ->with('success', '✏️ Category Updated Successfully');
    }

    /**
     * Soft delete category and cascade delete products.
     */
    public function destroy(Category $category)
    {
        DB::transaction(function () use ($category) {

            /*
             * Store category deletion history.
             */
            SoftDeleteHistory::create([
                'entity_type' => 'category',
                'entity_id' => $category->id,
                'parent_type' => null,
                'parent_id' => null,
                'action' => 'deleted',
                'deletion_source' => 'category',
                'event_at' => now(),
            ]);

            /*
             * Store history for all active products
             * that will be cascade soft deleted.
             */
            foreach ($category->products()->get() as $product) {
                SoftDeleteHistory::create([
                    'entity_type' => 'product',
                    'entity_id' => $product->id,
                    'parent_type' => 'category',
                    'parent_id' => $category->id,
                    'action' => 'deleted',
                    'deletion_source' => 'cascade',
                    'event_at' => now(),
                ]);
            }

            /*
             * CascadeSoftDeletes automatically
             * soft deletes related products.
             */
            $category->delete();
        });

        return redirect()
            ->route('categories.index')
            ->with(
                'success',
                '🗑 Category Deleted Successfully — Related Products Were Cascade Soft Deleted'
            );
    }
}