@extends('layouts.app')

@section('content')

<div class="page-header">

    <div>
        <h2>Products</h2>

        <p style="color:#64748b;">
            Manage active products and their categories.
        </p>
    </div>

    <a href="{{ route('products.create') }}"
       class="btn btn-success">

        + Add Product

    </a>

</div>


<div class="card">

    @if($products->count())

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

                        <form method="POST"
                              action="{{ route(
                                  'products.destroy',
                                  $p
                              ) }}"
                              onsubmit="return confirm(
                                  'Soft delete this product?'
                              )">

                            @csrf
                            @method('DELETE')

                            <button
                                class="btn btn-danger btn-sm">

                                🗑 Delete

                            </button>

                        </form>

                    </td>

                </tr>

            @endforeach

            </tbody>

        </table>

    @else

        <div class="empty-state">

            <h3>📦 No Products Found</h3>

            <p>
                Create a product to see it here.
            </p>

        </div>

    @endif

</div>

@endsection