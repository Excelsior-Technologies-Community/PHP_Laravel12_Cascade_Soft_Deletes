@extends('layouts.app')

@section('content')

<div class="page-header">

    <div>

        <h2>📦 Products</h2>

        <p style="color:#64748b;">
            Search, filter and manage active products.
        </p>

    </div>

    <a
        href="{{ route('products.create') }}"
        class="btn btn-success"
    >
        + Add Product
    </a>

</div>


{{-- Product Filters --}}

<div class="card filter-card">

    <form method="GET" action="{{ route('products.index') }}">

        <div class="filter-grid">

            {{-- Search --}}

            <div>

                <label>
                    🔎 Search Product
                </label>

                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Enter product name..."
                >

            </div>


            {{-- Category Filter --}}

            <div>

                <label>
                    🏷️ Category
                </label>

                <select name="category_id">

                    <option value="">
                        All Categories
                    </option>

                    @foreach($categories as $category)

                        <option
                            value="{{ $category->id }}"
                            {{ (string) $categoryId === (string) $category->id
                                ? 'selected'
                                : '' }}
                        >
                            {{ $category->name }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- Sorting --}}

            <div>

                <label>
                    ↕️ Sort By
                </label>

                <select name="sort">

                    <option
                        value="latest"
                        {{ $sort === 'latest' ? 'selected' : '' }}
                    >
                        Newest
                    </option>

                    <option
                        value="oldest"
                        {{ $sort === 'oldest' ? 'selected' : '' }}
                    >
                        Oldest
                    </option>

                    <option
                        value="name_asc"
                        {{ $sort === 'name_asc' ? 'selected' : '' }}
                    >
                        Name A-Z
                    </option>

                    <option
                        value="name_desc"
                        {{ $sort === 'name_desc' ? 'selected' : '' }}
                    >
                        Name Z-A
                    </option>

                    <option
                        value="category_asc"
                        {{ $sort === 'category_asc' ? 'selected' : '' }}
                    >
                        Category A-Z
                    </option>

                    <option
                        value="category_desc"
                        {{ $sort === 'category_desc' ? 'selected' : '' }}
                    >
                        Category Z-A
                    </option>

                </select>

            </div>


            {{-- Per Page --}}

            <div>

                <label>
                    📄 Per Page
                </label>

                <select name="per_page">

                    @foreach([5, 10, 25, 50] as $size)

                        <option
                            value="{{ $size }}"
                            {{ $perPage == $size ? 'selected' : '' }}
                        >
                            {{ $size }}
                        </option>

                    @endforeach

                </select>

            </div>

        </div>


        <div class="action-btns">

            <button class="btn btn-primary">
                🔍 Apply Filters
            </button>

            <a
                href="{{ route('products.index') }}"
                class="btn btn-warning"
            >
                ↻ Reset
            </a>

        </div>

    </form>

</div>


{{-- Product Table --}}

<div class="card">

    <div class="section-header">

        <h3>
            📦 Product List
        </h3>

        <span class="badge">
            {{ $products->total() }} Products
        </span>

    </div>


    @if($products->count())

        <div class="table-wrapper">

            <table class="table">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Action</th>

                    </tr>

                </thead>

                <tbody>

                @foreach($products as $p)

                    <tr>

                        <td>
                            #{{ $p->id }}
                        </td>

                        <td>

                            <strong>
                                {{ $p->name }}
                            </strong>

                        </td>

                        <td>

                            @if($p->category)

                                {{ $p->category->name }}

                            @else

                                <span class="status-deleted">
                                    Category Unavailable
                                </span>

                            @endif

                        </td>

                        <td>

                            <form
                                method="POST"
                                action="{{ route(
                                    'products.destroy',
                                    $p
                                ) }}"
                                onsubmit="return confirm(
                                    'Soft delete this product?'
                                )"
                            >

                                @csrf
                                @method('DELETE')

                                <button
                                    class="btn btn-danger btn-sm"
                                >
                                    🗑 Delete
                                </button>

                            </form>

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>


        <div class="pagination-wrapper">

            {{ $products->links() }}

        </div>

    @else

        <div class="empty-state">

            <h3>📦 No Products Found</h3>

            <p>
                No products match your current filters.
            </p>

        </div>

    @endif

</div>

@endsection