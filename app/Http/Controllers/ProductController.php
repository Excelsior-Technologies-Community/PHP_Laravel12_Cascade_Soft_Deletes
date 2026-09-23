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
     * Display active products with search,
     * category filter, sorting and pagination.
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $categoryId = $request->input('category_id');
        $sort = $request->input('sort', 'oldest');

        $perPage = (int) $request->input('per_page', 5);

        if (!in_array($perPage, [5, 10, 25, 50], true)) {
            $perPage = 5;
        }

        $query = Product::with('category');

        /*
         * 4. Product name search
         */
        if ($search !== '') {
            $query->where('name', 'like', '%' . $search . '%');
        }

        /*
         * 5. Product category filter
         */
        if ($categoryId !== null && $categoryId !== '') {
            $query->where('category_id', $categoryId);
        }

        /*
         * 6. Product sorting
         */
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;

            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;

            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;

            case 'category_asc':
                $query
                    ->leftJoin(
                        'categories',
                        'products.category_id',
                        '=',
                        'categories.id'
                    )
                    ->select('products.*')
                    ->orderBy('categories.name', 'asc');
                break;

            case 'category_desc':
                $query
                    ->leftJoin(
                        'categories',
                        'products.category_id',
                        '=',
                        'categories.id'
                    )
                    ->select('products.*')
                    ->orderBy('categories.name', 'desc');
                break;

            default:
                $query->oldest();
                break;
        }

        /*
         * 7. Product pagination
         */
        $products = $query
            ->paginate($perPage)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('products.index', compact(
            'products',
            'categories',
            'search',
            'categoryId',
            'sort',
            'perPage'
        ));
    }

    /**
     * Show product creation form.
     */
    public function create()
    {
        $categories = Category::oldest()->get();

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