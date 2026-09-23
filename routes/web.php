<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SoftDeleteController;


/*
|--------------------------------------------------------------------------
| Homepage
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect('categories'));


/*
|--------------------------------------------------------------------------
| Category CRUD
|--------------------------------------------------------------------------
*/

Route::resource(
    'categories',
    CategoryController::class
);


/*
|--------------------------------------------------------------------------
| Product CRUD
|--------------------------------------------------------------------------
*/

Route::resource(
    'products',
    ProductController::class
);


/*
|--------------------------------------------------------------------------
| Soft Delete Management
|--------------------------------------------------------------------------
*/

Route::get(
    '/soft-deletes',
    [SoftDeleteController::class, 'index']
)->name('soft-deletes.index');


Route::get(
    '/soft-deletes/analytics',
    [SoftDeleteController::class, 'analytics']
)->name('soft-deletes.analytics');


Route::post(
    '/soft-deletes/categories/{id}/restore',
    [SoftDeleteController::class, 'restoreCategory']
)->name('soft-deletes.categories.restore');


Route::post(
    '/soft-deletes/products/{id}/restore',
    [SoftDeleteController::class, 'restoreProduct']
)->name('soft-deletes.products.restore');


Route::delete(
    '/soft-deletes/categories/{id}/force',
    [SoftDeleteController::class, 'forceDeleteCategory']
)->name('soft-deletes.categories.force');


Route::delete(
    '/soft-deletes/products/{id}/force',
    [SoftDeleteController::class, 'forceDeleteProduct']
)->name('soft-deletes.products.force');