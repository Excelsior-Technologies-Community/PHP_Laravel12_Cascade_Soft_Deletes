#  PHP_Laravel12_Cascade_Soft_Deletes

<p align="center">
<img src="https://img.shields.io/badge/Laravel-12-red" alt="Laravel Version">
<img src="https://img.shields.io/badge/PHP-8%2B-blue" alt="PHP Version">
<img src="https://img.shields.io/badge/MySQL-Database-orange" alt="Database">
<img src="https://img.shields.io/badge/Blade-Admin%20UI-green" alt="Blade UI">
<img src="https://img.shields.io/badge/Cascade-Soft%20Deletes-purple" alt="Cascade Soft Deletes">
</p>

---

##  Overview

This project demonstrates how to build a **Laravel 12 Admin Panel** from scratch with:

* Category & Product CRUD
* Cascade Soft Deletes
* Blade Admin UI
* Flash Messages
* Full MVC Structure

When a **Category** is deleted, all related **Products** are automatically soft deleted using the `dyrynda/laravel-cascade-soft-deletes` package.

---

##  Features

*  Category CRUD (Create, Read, Update, Delete)
*  Product CRUD
*  Cascade Soft Deletes
*  Clean Blade Admin Panel UI
*  Flash Success & Error Messages
*  MVC Architecture
*  Automatic Homepage Redirect

---

##  Folder Structure

```
app/
 ├── Http/
 │    └── Controllers/
 │         ├── CategoryController.php
 │         └── ProductController.php
 │
 └── Models/
      ├── Category.php
      └── Product.php

resources/
 └── views/
      ├── layouts/
      │     └── app.blade.php
      ├── categories/
      │     ├── index.blade.php
      │     ├── create.blade.php
      │     └── edit.blade.php
      └── products/
            ├── index.blade.php
            └── create.blade.php

routes/
 └── web.php
```

---

## Step 1 — Create Laravel Project

```bash
composer create-project laravel/laravel cascade-project
```

---

## Step 2 — Install Cascade Soft Delete Package

```bash
composer require dyrynda/laravel-cascade-soft-deletes
```

---

## Step 3 — Database Configuration

Update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

---

## Step 4 — Create Models & Controllers

```bash
php artisan make:model Category -m
php artisan make:model Product -m
```

---

## Step 5 — Migrations

### Categories Migration

```php
public function up(): void
{
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->softDeletes(); // IMPORTANT
        $table->timestamps();
    });
}
```

### Products Migration

```php
public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->foreignId('category_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->string('name');
        $table->softDeletes(); // IMPORTANT
        $table->timestamps();
    });
}
```

Run:

```bash
php artisan migrate
```

---

## Step 6 — Models

### app/Models/Category.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;

class Category extends Model
{
    use SoftDeletes, CascadeSoftDeletes;

    protected $fillable = ['name'];

    protected $cascadeDeletes = ['products'];

    protected $dates = ['deleted_at'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
```

### app/Models/Product.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
```

---

## Step 7 — Controllers

```bash
php artisan make:controller CategoryController --resource
php artisan make:controller ProductController --resource
```

### app/Http/Controllers/CategoryController.php

```php
<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->latest()->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        Category::create($request->all());

        return redirect()->route('categories.index')
            ->with('success','✅ Category Created Successfully');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $category->update($request->all());

        return redirect()->route('categories.index')
            ->with('success','✏️ Category Updated Successfully');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success','🗑 Category Deleted (Products also deleted)');
    }
}
```

### app/Http/Controllers/ProductController.php

```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required'
        ]);

        Product::create($request->all());

        return redirect()->route('products.index')
            ->with('success','✅ Product Created Successfully');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return back()->with('success','🗑 Product Deleted Successfully');
    }
}
```

---

## Step 8 — Routes

### routes/web.php

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

Route::get('/', fn() => redirect('categories'));

Route::resource('categories', CategoryController::class);
Route::resource('products', ProductController::class);
```

---

## Step 9 — Blade Layout

### resources/views/layouts/app.blade.php

```html
<!DOCTYPE html>
<html>
<head>
    <title>Laravel Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

<style>

body{
    font-family: Arial;
    background:#f3f4f6;
    margin:0;
}

/* NAVBAR */
.navbar{
    background:#0f172a;
    padding:15px 30px;
}

.navbar a{
    color:white;
    text-decoration:none;
    margin-right:20px;
    font-weight:600;
}

/* CONTAINER */
.container{
    width:90%;
    margin:30px auto;
}

/* HEADER */
.page-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

/* CARD */
.card{
    background:white;
    border-radius:10px;
    padding:20px;
    box-shadow:0 4px 15px rgba(0,0,0,0.06);
}

/* TABLE */
.table{
    width:100%;
    border-collapse:collapse;
}

.table thead{
    background:#0f172a;
    color:white;
}

.table th{
    padding:14px;
    text-align:left;
}

.table td{
    padding:14px;
    border-bottom:1px solid #eee;
}

.table tbody tr:hover{
    background:#f9fafb;
}

/* BUTTONS */
.btn{
    border:none;
    padding:8px 14px;
    border-radius:6px;
    color:white;
    cursor:pointer;
    text-decoration:none;
}

.btn-success{background:#16a34a;}
.btn-danger{background:#dc2626;}
.btn-warning{background:#f59e0b;}
.btn-primary{background:#2563eb;}

.btn-sm{
    padding:6px 10px;
    font-size:13px;
}

/* BADGE */
.badge{
    background:#2563eb;
    color:white;
    padding:5px 10px;
    border-radius:20px;
}

/* ACTION */
.action-btns{
    display:flex;
    gap:8px;
}

/* FORM */
input,select{
    width:100%;
    padding:10px;
    margin-top:10px;
    margin-bottom:15px;
}

/* ALERT */
.alert{
    padding:12px;
    margin-bottom:15px;
    border-radius:6px;
}

.success{
    background:#dcfce7;
    color:#166534;
}

.error{
    background:#fee2e2;
    color:#991b1b;
}

</style>
</head>

<body>

<div class="navbar">
    <a href="/categories">Categories</a>
    <a href="/products">Products</a>
</div>

<div class="container">

@if(session('success'))
<div class="alert success">
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="alert error">
<ul>
@foreach($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif

@yield('content')

</div>

<script>
setTimeout(()=>{
document.querySelectorAll('.alert')
.forEach(el=>el.style.display='none');
},3000);
</script>

</body>
</html>
```

---

## Step 10 — Category Views

### resources/views/categories/index.blade.php

```blade
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
```

### resources/views/categories/create.blade.php

```blade
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
```

### resources/views/categories/edit.blade.php

```blade
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
```

---

## Step 11 — Product Views

### resources/views/products/index.blade.php

```blade
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
```

### resources/views/products/create.blade.php

```blade
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
```

---

## Step 12 — Run Project

```bash
php artisan serve
```

Open:

```
http://127.0.0.1:8000
```

Homepage Redirect:

```php
Route::get('/', fn() => redirect('categories'));
```

When user opens:

```
http://localhost:8000/
```

It automatically opens:

```
/categories
```

---

## Result

* Category Create & Edit

  <img width="1788" height="327" alt="Screenshot 2026-02-19 115520" src="https://github.com/user-attachments/assets/20a4a65f-689c-40c0-8b8f-78a8d17c9015" />

  <img width="1770" height="377" alt="Screenshot 2026-02-19 122013" src="https://github.com/user-attachments/assets/f2b07471-b79f-41e3-8b9e-89d5ac09d7c3" />


* Product Create

  <img width="1775" height="322" alt="Screenshot 2026-02-19 115551" src="https://github.com/user-attachments/assets/b0692a00-4573-4843-a770-225e2bbed9b7" />

* Cascade Soft Delete Working

  Category:

  <img width="1758" height="370" alt="Screenshot 2026-02-19 115730" src="https://github.com/user-attachments/assets/2a1d70d4-bb43-4dea-9ebe-dac07e119667" />

  <img width="852" height="285" alt="Screenshot 2026-02-19 115821" src="https://github.com/user-attachments/assets/6f4f89ad-9ea5-4f61-8810-4be17ae810b8" />


  Product:

  <img width="1760" height="317" alt="Screenshot 2026-02-19 115756" src="https://github.com/user-attachments/assets/1d037c76-7275-4ea2-a9ea-ba4a8c7c9b88" />

  <img width="959" height="281" alt="Screenshot 2026-02-19 115842" src="https://github.com/user-attachments/assets/a35f9897-8e01-4754-b196-778fb8e67827" />


---





