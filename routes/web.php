<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

// Redirect homepage to categories listing page
Route::get('/', fn() => redirect('categories'));

// Resource routes for Category CRUD operations
Route::resource('categories', CategoryController::class);

// Resource routes for Product CRUD operations
Route::resource('products', ProductController::class);
