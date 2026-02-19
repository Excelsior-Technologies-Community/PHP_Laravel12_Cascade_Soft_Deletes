@extends('layouts.app')

@section('content')

<h2>Create Category</h2>

<div class="card">

<form method="POST" action="{{ route('categories.store') }}">
@csrf

<label>Name</label>
<input type="text" name="name" placeholder="Category name">

<button class="btn btn-primary">Save</button>

</form>

</div>

@endsection
