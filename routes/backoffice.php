<?php

use App\Enums\UserType;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
/*
|--------------------------------------------------------------------------
| Backoffice Routes
|--------------------------------------------------------------------------
|
*/

$resourceMethod = ['index', 'create', 'edit', 'store', 'update', 'destroy'];

Route::middleware(['auth', "role: " . implode("|", [UserType::Admin])])
  ->prefix('admin')
  ->name('admin.')
  ->group(function () use ($resourceMethod) {
    Route::resource('users', Admin\UsersController::class)->only([...$resourceMethod]);
    Route::resource('categories', Admin\CategoryController::class)->only([...$resourceMethod]);
  });

Route::middleware(['auth', "role: " . implode("|", [UserType::Admin, UserType::Partner])])
  ->prefix('admin')
  ->name('admin.')
  ->group(function () use ($resourceMethod) {
    Route::resource('transactions', Admin\TransactionController::class)->only([...$resourceMethod]);
  });
