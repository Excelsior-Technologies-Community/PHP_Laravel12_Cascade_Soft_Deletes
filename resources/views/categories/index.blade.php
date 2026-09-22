@extends('layouts.app')

@section('content')

<div class="page-header">

    <div>

        <h2>Categories</h2>

        <p style="color:#64748b;">
            Deleting a category automatically soft deletes its products.
        </p>

    </div>

    <a
        href="{{ route('categories.create') }}"
        class="btn btn-success"
    >
        + Add Category
    </a>

</div>


<div class="card">

    @if($categories->count())

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
                                Edit
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

    @else

        <div class="empty-state">

            <h3>📁 No Categories Found</h3>

            <p>
                Create your first category.
            </p>

        </div>

    @endif

</div>

@endsection