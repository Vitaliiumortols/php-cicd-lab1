<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', [
        'counts' => [
            'products' => Product::count(),
            'categories' => Category::count(),
            'tags' => Tag::count(),
            'users' => User::count(),
            'carts' => Cart::count(),
            'orders' => Order::count(),
            'shipments' => Shipment::count(),
        ],
    ]);
})->name('home');

Route::get('/health', fn () => response()->json(['status' => 'OK']));

Route::resource('catalog', ProductController::class)->parameters(['catalog' => 'product']);
Route::resource('categories', CategoryController::class);
Route::resource('tags', TagController::class);
Route::resource('users', UserController::class);
Route::resource('carts', CartController::class);
Route::post('/carts/{cart}/items', [CartController::class, 'addItem'])->name('carts.items.store');
Route::delete('/carts/{cart}/items/{cartItem}', [CartController::class, 'removeItem'])->name('carts.items.destroy');
Route::resource('orders', OrderController::class);
Route::resource('shipments', ShipmentController::class);
Route::post('/orders/{order}/shipment', [ShipmentController::class, 'storeForOrder'])->name('orders.shipment.store');
