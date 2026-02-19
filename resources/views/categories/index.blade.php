@extends('layouts.app')

@section('content')

<div class="page-header">
<h2>Categories</h2>

<a href="{{ route('categories.create') }}" class="btn btn-success">
+ Add Category
</a>
</div>

<div class="card">

<table class="table">
<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>Products</th>
<th>Action</th>
</tr>
</thead>

<tbody>

@foreach($categories as $c)
<tr>
<td>#{{ $c->id }}</td>
<td>{{ $c->name }}</td>

<td>
<span class="badge">
{{ $c->products_count }}
</span>
</td>

<td>
<div class="action-btns">

<a href="{{ route('categories.edit',$c) }}"
class="btn btn-warning btn-sm">Edit</a>

<form method="POST"
action="{{ route('categories.destroy',$c) }}"
onsubmit="return confirm('Delete?')">
@csrf
@method('DELETE')
<button class="btn btn-danger btn-sm">Delete</button>
</form>

</div>
</td>
</tr>
@endforeach

</tbody>
</table>

</div>

@endsection
