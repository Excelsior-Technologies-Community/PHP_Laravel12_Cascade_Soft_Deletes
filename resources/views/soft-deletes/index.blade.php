@extends('layouts.app')

@section('content')

<div class="page-header">

    <div>

        <h2>🗑️ Soft Deleted Records</h2>

        <p style="color:#64748b;">
            Search deleted records, filter deletion source,
            restore records or permanently remove them.
        </p>

    </div>

    <a
        href="{{ route('soft-deletes.analytics') }}"
        class="btn btn-primary"
    >
        📊 View Analytics
    </a>

</div>


{{-- Deleted Record Filters --}}

<div class="card filter-card">

    <form
        method="GET"
        action="{{ route('soft-deletes.index') }}"
    >

        <div class="filter-grid">

            <div>

                <label>
                    🔎 Search Deleted Records
                </label>

                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search category or product..."
                >

            </div>


            <div>

                <label>
                    🎯 Delete Source
                </label>

                <select name="source">

                    <option value="">
                        All Sources
                    </option>

                    <option
                        value="direct"
                        {{ $source === 'direct' ? 'selected' : '' }}
                    >
                        🗑 Direct Delete
                    </option>

                    <option
                        value="cascade"
                        {{ $source === 'cascade' ? 'selected' : '' }}
                    >
                        🔄 Cascade Delete
                    </option>

                </select>

            </div>


            <div>

                <label>
                    📁 Category Per Page
                </label>

                <select name="category_per_page">

                    @foreach([5, 10, 25, 50] as $size)

                        <option
                            value="{{ $size }}"
                            {{ $categoryPerPage == $size
                                ? 'selected'
                                : '' }}
                        >
                            {{ $size }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div>

                <label>
                    📦 Product Per Page
                </label>

                <select name="product_per_page">

                    @foreach([5, 10, 25, 50] as $size)

                        <option
                            value="{{ $size }}"
                            {{ $productPerPage == $size
                                ? 'selected'
                                : '' }}
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
                href="{{ route('soft-deletes.index') }}"
                class="btn btn-warning"
            >
                ↻ Reset
            </a>

        </div>

    </form>

</div>


{{-- Deleted Categories --}}

<div
    class="card"
    style="margin-bottom:25px;"
>

    <div class="section-header">

        <h3>
            📁 Deleted Categories
        </h3>

        <span class="badge">
            {{ $deletedCategories->total() }}
        </span>

    </div>


    @if($deletedCategories->count())

        <div class="table-wrapper">

            <table class="table">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Category</th>
                        <th>Affected Products</th>
                        <th>Deleted At</th>
                        <th>Recovery</th>

                    </tr>

                </thead>

                <tbody>

                @foreach($deletedCategories as $category)

                    @php
                        $affectedProducts = $category->products
                            ->whereNotNull('deleted_at')
                            ->count();
                    @endphp

                    <tr>

                        <td>
                            #{{ $category->id }}
                        </td>

                        <td>
                            <strong>
                                {{ $category->name }}
                            </strong>
                        </td>

                        <td>

                            <span class="badge">
                                {{ $affectedProducts }} Products
                            </span>

                        </td>

                        <td>
                            {{ $category->deleted_at?->format(
                                'd M Y h:i A'
                            ) }}
                        </td>

                        <td>

                            <div class="action-btns">

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'soft-deletes.categories.restore',
                                        $category->id
                                    ) }}"
                                >

                                    @csrf

                                    <button
                                        class="btn btn-success btn-sm"
                                        onclick="return confirm(
                                            'Restore this category and its cascade-deleted products?'
                                        )"
                                    >
                                        ♻️ Restore
                                    </button>

                                </form>


                                <form
                                    method="POST"
                                    action="{{ route(
                                        'soft-deletes.categories.force',
                                        $category->id
                                    ) }}"
                                    onsubmit="return confirm(
                                        'Permanently delete this category and all related products? This cannot be undone.'
                                    )"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        class="btn btn-danger btn-sm"
                                    >
                                        🔥 Delete Forever
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>


        <div class="pagination-wrapper">

            {{ $deletedCategories->links() }}

        </div>

    @else

        <div class="empty-state">

            <h3>✅ No Deleted Categories</h3>

            <p>
                No deleted categories match your filters.
            </p>

        </div>

    @endif

</div>


{{-- Deleted Products --}}

<div class="card">

    <div class="section-header">

        <h3>
            📦 Deleted Products
        </h3>

        <span class="badge">
            {{ $deletedProducts->total() }}
        </span>

    </div>


    @if($deletedProducts->count())

        <div class="table-wrapper">

            <table class="table">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Delete Source</th>
                        <th>Deleted At</th>
                        <th>Recovery</th>

                    </tr>

                </thead>

                <tbody>

                @foreach($deletedProducts as $product)

                    @php

                        $history =
                            \App\Models\SoftDeleteHistory::where(
                                'entity_type',
                                'product'
                            )
                            ->where(
                                'entity_id',
                                $product->id
                            )
                            ->latest()
                            ->first();

                    @endphp

                    <tr>

                        <td>
                            #{{ $product->id }}
                        </td>

                        <td>
                            <strong>
                                {{ $product->name }}
                            </strong>
                        </td>

                        <td>

                            @if($product->category)

                                {{ $product->category->name }}

                                @if($product->category->trashed())

                                    <span class="status-deleted">
                                        Category Deleted
                                    </span>

                                @endif

                            @else

                                <span class="status-deleted">
                                    Category Missing
                                </span>

                            @endif

                        </td>

                        <td>

                            @if(
                                $history?->deletion_source === 'cascade'
                            )

                                <span class="badge badge-cascade">
                                    🔄 Cascade Delete
                                </span>

                            @else

                                <span class="badge badge-direct">
                                    🗑 Direct Delete
                                </span>

                            @endif

                        </td>

                        <td>
                            {{ $product->deleted_at?->format(
                                'd M Y h:i A'
                            ) }}
                        </td>

                        <td>

                            @if(
                                $product->category &&
                                !$product->category->trashed()
                            )

                                <div class="action-btns">

                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'soft-deletes.products.restore',
                                            $product->id
                                        ) }}"
                                    >

                                        @csrf

                                        <button
                                            class="btn btn-success btn-sm"
                                        >
                                            ♻️ Restore
                                        </button>

                                    </form>


                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'soft-deletes.products.force',
                                            $product->id
                                        ) }}"
                                        onsubmit="return confirm(
                                            'Permanently delete this product?'
                                        )"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            class="btn btn-danger btn-sm"
                                        >
                                            🔥 Delete Forever
                                        </button>

                                    </form>

                                </div>

                            @else

                                <span class="restore-warning">
                                    Restore Category First
                                </span>

                            @endif

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>


        <div class="pagination-wrapper">

            {{ $deletedProducts->links() }}

        </div>

    @else

        <div class="empty-state">

            <h3>✅ No Deleted Products</h3>

            <p>
                No deleted products match your filters.
            </p>

        </div>

    @endif

</div>

@endsection