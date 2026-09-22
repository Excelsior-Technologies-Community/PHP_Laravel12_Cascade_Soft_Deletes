@extends('layouts.app')

@section('content')

<div class="page-header">

    <h2>Create Product</h2>

    <a href="{{ route('products.index') }}"
       class="btn btn-warning">

        ← Back

    </a>

</div>


<div class="card">

    @if($categories->count())

        <form method="POST"
              action="{{ route('products.store') }}">

            @csrf


            <label>
                Product Name
            </label>

            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                placeholder="Enter product name"
                required
            >


            <label>
                Category
            </label>

            <select
                name="category_id"
                required
            >

                <option value="">
                    Select Category
                </option>

                @foreach($categories as $cat)

                    <option
                        value="{{ $cat->id }}"
                        {{ old('category_id') == $cat->id
                            ? 'selected'
                            : '' }}
                    >

                        {{ $cat->name }}

                    </option>

                @endforeach

            </select>


            <button class="btn btn-primary">
                Save Product
            </button>

        </form>

    @else

        <div class="empty-state">

            <h3>⚠️ No Categories Available</h3>

            <p>
                Create a category before adding a product.
            </p>

            <a
                href="{{ route('categories.create') }}"
                class="btn btn-primary"
            >
                + Create Category
            </a>

        </div>

    @endif

</div>

@endsection