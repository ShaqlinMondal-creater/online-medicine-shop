<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);


Route::get('/admin/stats', [AdminController::class, 'getStats']);
// API endpoints for Products
Route::get('/admin/get-products', [AdminController::class, 'getProducts']);
Route::post('/admin/update-product', [AdminController::class, 'updateProduct']);
Route::delete('/admin/delete-product', [AdminController::class, 'deleteProduct']);