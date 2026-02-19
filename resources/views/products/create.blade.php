@extends('layouts.app')

@section('content')

<h2>Create Product</h2>

<div class="card">

<form method="POST" action="{{ route('products.store') }}">
@csrf

<label>Product Name</label>
<input name="name">

<label>Category</label>
<select name="category_id">
@foreach($categories as $cat)
<option value="{{ $cat->id }}">
{{ $cat->name }}
</option>
@endforeach
</select>

<button class="btn btn-primary">Save</button>

</form>

</div>

@endsection
