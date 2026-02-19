<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Display all products with their category
    public function index()
    {
        $products = Product::with('category')->latest()->get();
        return view('products.index', compact('products'));
    }

    // Show product creation form
    public function create()
    {
        // Get all categories for dropdown
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    // Store new product
    public function store(Request $request)
    {
        // Validate product data
        $request->validate([
            'name' => 'required',
            'category_id' => 'required'
        ]);

        // Create product record
        Product::create($request->all());

        // Redirect with success message
        return redirect()->route('products.index')
            ->with('success','✅ Product Created Successfully');
    }

    // Delete selected product
    public function destroy(Product $product)
    {
        // Soft delete product
        $product->delete();

        // Redirect back with success message
        return back()->with('success','🗑 Product Deleted Successfully');
    }
}
