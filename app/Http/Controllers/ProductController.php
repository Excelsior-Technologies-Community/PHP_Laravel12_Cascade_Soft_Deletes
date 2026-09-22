<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\SoftDeleteHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display all active products with their category.
     */
    public function index()
    {
        $products = Product::with('category')
            ->latest()
            ->get();

        return view('products.index', compact('products'));
    }

    /**
     * Show product creation form.
     */
    public function create()
    {
        $categories = Category::latest()->get();

        return view('products.create', compact('categories'));
    }

    /**
     * Store new product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'category_id' => [
                'required',
                'exists:categories,id',
            ],
        ]);

        Product::create($validated);

        return redirect()
            ->route('products.index')
            ->with('success', '✅ Product Created Successfully');
    }

    /**
     * Delete selected product.
     */
    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {

            SoftDeleteHistory::create([
                'entity_type' => 'product',
                'entity_id' => $product->id,
                'parent_type' => 'category',
                'parent_id' => $product->category_id,
                'action' => 'deleted',
                'deletion_source' => 'direct',
                'event_at' => now(),
            ]);

            $product->delete();
        });

        return back()
            ->with('success', '🗑 Product Deleted Successfully');
    }
}