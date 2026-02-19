@extends('layouts.app')

@section('content')

<h2>Edit Category</h2>

<div class="card">

<form method="POST"
action="{{ route('categories.update',$category) }}">
@csrf
@method('PUT')

<label>Name</label>
<input type="text" name="name"
value="{{ $category->name }}">

<button class="btn btn-primary">Update</button>

</form>

</div>

@endsection
