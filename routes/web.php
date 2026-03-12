<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderController; //adding order controller to routes

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/photo/update', [UserController::class, 'updateProfilePhoto'])->name('profile.photo.update');
    Route::get('/profile/photo/{filename}', [UserController::class, 'showProfilePhoto'])->where('filename', '.*')->name('user.photo'); //to read
    Route::resource('products', ProductController::class);
    Route:: resource('payments', PaymentController::class);

    //order routes & order status update routes (between 2 controllers: payment and order)
    Route::resource('orders', OrderController::class);
    Route::patch('/orders/{order}/mark-as-packing', [OrderController::class, 'markAsPacking'])->name('orders.markAsPacking');
    Route::patch('/orders/{order}/mark-as-delivering', [OrderController::class, 'markAsDelivering'])->name('orders.markAsDelivering');
    Route::patch('/orders/{order}/mark-as-complete', [OrderController::class, 'markAsComplete'])->name('orders.markAsComplete');
});


require __DIR__.'/auth.php';
