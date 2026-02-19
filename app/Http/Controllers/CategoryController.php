<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Display all categories with product count
    public function index()
    {
        $categories = Category::withCount('products')->latest()->get();
        return view('categories.index', compact('categories'));
    }

    // Show category create form
    public function create()
    {
        return view('categories.create');
    }

    // Store new category in database
    public function store(Request $request)
    {
        // Validate request data
        $request->validate([
            'name' => 'required'
        ]);

        // Create category record
        Category::create($request->all());

        // Redirect with success message
        return redirect()->route('categories.index')
            ->with('success','✅ Category Created Successfully');
    }

    // Show edit form for selected category
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    // Update category data
    public function update(Request $request, Category $category)
    {
        // Validate input
        $request->validate([
            'name' => 'required'
        ]);

        // Update category
        $category->update($request->all());

        // Redirect with success message
        return redirect()->route('categories.index')
            ->with('success','✏️ Category Updated Successfully');
    }

    // Soft delete category (cascade deletes products)
    public function destroy(Category $category)
    {
        // Delete category
        $category->delete();

        // Redirect back with success message
        return redirect()->route('categories.index')
            ->with('success','🗑 Category Deleted (Products also deleted)');
    }
}
