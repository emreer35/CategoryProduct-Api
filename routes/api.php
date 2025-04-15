<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Auth\CategoryController;
use App\Http\Controllers\Api\Auth\ProductController;
use App\Http\Controllers\Api\Auth\ReviewController;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// Admin
Route::middleware(['auth:api','isAdmin'])->prefix('admin')->group(function(){
    // User
    Route::get('/users', [AdminAuthController::class, 'index']);
    Route::get('/users/{user}', [AdminAuthController::class,'show']);
    Route::post('user/create',[AdminAuthController::class,'store']);
    Route::put('user/{user}/update',[AdminAuthController::class,'update']);
    Route::delete('user/{user}/delete',[AdminAuthController::class,'destroy']);

    // Category // index - show - create - update - destroy
    Route::get('/categories',[AdminCategoryController::class,'index']);
    Route::get('categories/{category}',[AdminCategoryController::class,'show']);
    Route::post('category/create',[AdminCategoryController::class,'store']);
    Route::put('categories/{category}/update', [AdminCategoryController::class, 'update']);
    Route::delete('/categories/{category}/delete',[AdminCategoryController::class,'destroy']);

    // Product // index - show - create - update - destroy

    Route::get('products',[AdminProductController::class,'index']);
    Route::get('products/{product}',[AdminProductController::class,'show']);
    Route::get('categories/{category}/products',[AdminProductController::class,'getProductsByCategory']);
    Route::post('/product/create',[AdminProductController::class,'store']);
    Route::put('/products/{product}/update',[AdminProductController::class,'update']);
    Route::delete('/products/{product}/delete',[AdminProductController::class,'destroy']);

    // Review // 

});

//Auth 
Route::post('register', [AuthController::class,'register']);
Route::post('login',[AuthController::class,'login']);

Route::middleware('auth:api')->group(function(){


    // Auth 
    Route::prefix('auth')->group(function(){
        Route::get('me', [AuthController::class, 'me']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::delete('logout', [AuthController::class,'logout']);
    });

    // Category
    Route::get('/categories', [CategoryController::class,'index']);
    Route::get('/category/{category}',[CategoryController::class,'show']);

    // Product 
    Route::get('/products',[ProductController::class,'index']);
    Route::get('/products/{product}',[ProductController::class,'show']);
    Route::get('/categories/{category}/products',[ProductController::class, 'getByCategory']);

    // Review 
    Route::get('/reviews',[ReviewController::class, 'index']);
    Route::post('/products/{product}/review/create',[ReviewController::class,'store']);
    Route::get('/user/reviews', [ReviewController::class, 'getUserReviews']);
    Route::get('/reviews/{review}', [ReviewController::class,'show']);
    Route::put('/products/{product}/reviews/{review}/update',[ReviewController::class,'update']);
    Route::delete('/reviews/{review}/delete',[ReviewController::class,'destroy']);
    

});



