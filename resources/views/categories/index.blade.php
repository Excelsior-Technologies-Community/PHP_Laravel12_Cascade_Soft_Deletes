@extends('layouts.app')

@section('content')

<div class="page-header">

    <div>
        <h2>📁 Categories</h2>

        <p style="color:#64748b;">
            Manage categories and their cascade soft-delete behavior.
        </p>
    </div>

    <a
        href="{{ route('categories.create') }}"
        class="btn btn-success"
    >
        + Add Category
    </a>

</div>


{{-- Category Filters --}}

<div class="card filter-card">

    <form method="GET" action="{{ route('categories.index') }}">

        <div class="filter-grid">

            <div>
                <label>🔎 Search Category</label>

                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Enter category name..."
                >
            </div>


            <div>
                <label>↕️ Sort By</label>

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
                        value="products_desc"
                        {{ $sort === 'products_desc' ? 'selected' : '' }}
                    >
                        Most Products
                    </option>

                    <option
                        value="products_asc"
                        {{ $sort === 'products_asc' ? 'selected' : '' }}
                    >
                        Least Products
                    </option>

                </select>
            </div>


            <div>
                <label>📄 Per Page</label>

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
                href="{{ route('categories.index') }}"
                class="btn btn-warning"
            >
                ↻ Reset
            </a>

        </div>

    </form>

</div>


<div class="card">

    <div class="section-header">

        <h3>
            📁 Category List
        </h3>

        <span class="badge">
            {{ $categories->total() }} Categories
        </span>

    </div>


    @if($categories->count())

        <div class="table-wrapper">

            <table class="table">

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Products</th>
                        <th>Delete Behavior</th>
                        <th>Action</th>
                    </tr>

                </thead>

                <tbody>

                @foreach($categories as $c)

                    <tr>

                        <td>
                            #{{ $c->id }}
                        </td>

                        <td>
                            <strong>
                                {{ $c->name }}
                            </strong>
                        </td>

                        <td>

                            <span class="badge">
                                {{ $c->products_count }}
                            </span>

                        </td>

                        <td>

                            <span class="badge badge-cascade">
                                🔄 Cascade Soft Delete
                            </span>

                        </td>

                        <td>

                            <div class="action-btns">

                                <a
                                    href="{{ route(
                                        'categories.edit',
                                        $c
                                    ) }}"
                                    class="btn btn-warning btn-sm"
                                >
                                    ✏️ Edit
                                </a>


                                <form
                                    method="POST"
                                    action="{{ route(
                                        'categories.destroy',
                                        $c
                                    ) }}"
                                    onsubmit="return confirm(
                                        'Delete this category? All related products will also be soft deleted.'
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

                            </div>

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}

        <div class="pagination-wrapper">

            {{ $categories->links() }}

        </div>

    @else

        <div class="empty-state">

            <h3>📁 No Categories Found</h3>

            <p>
                No categories match your current filters.
            </p>

        </div>

    @endif

</div>

@endsection