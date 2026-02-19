@extends('layouts.app')

@section('content')

<div class="page-header">
<h2>Products</h2>

<a href="{{ route('products.create') }}"
class="btn btn-success">+ Add Product</a>
</div>

<div class="card">

<table class="table">

<thead>
<tr>
<th>Name</th>
<th>Category</th>
<th>Action</th>
</tr>
</thead>

<tbody>

@foreach($products as $p)
<tr>
<td>{{ $p->name }}</td>
<td>{{ $p->category->name }}</td>

<td>
<form method="POST"
action="{{ route('products.destroy',$p) }}">
@csrf
@method('DELETE')

<button class="btn btn-danger btn-sm">
Delete
</button>

</form>
</td>
</tr>
@endforeach

</tbody>

</table>

</div>

@endsection
