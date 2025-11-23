<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PostController;

//Route::apiResource('users', UserController::class);
//Route::apiResource('posts', PostController::class);

Route::post('/users', [UserController::class, 'store'])->name('api.users.store');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('api.users.delete');
Route::put('/users/{id}', [UserController::class, 'update'])->name('api.users.update');
Route::get('/users/{id}', [UserController::class, 'show'])->name('api.users.detail');
Route::get('/users', [UserController::class, 'index'])->name('api.users.list');

Route::post('/posts', [PostController::class, 'store'])->name('api.post.store');
Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('api.post.delete');
Route::put('/posts/{id}', [PostController::class, 'update'])->name('api.post.update');
Route::get('/posts/{id}', [PostController::class, 'show'])->name('api.post.detail');
Route::get('/posts', [PostController::class, 'index'])->name('api.post.list');
