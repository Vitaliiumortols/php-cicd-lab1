<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LegacyProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'project' => 'iPhoneHub',
        'laboratory' => 6,
        'variant' => 18,
        'student' => 'Томенко Віталій Ігорович, КН-42',
        'database' => 'SQLite + Eloquent ORM',
    ]);
});

Route::get('/health', fn () => response()->json(['status' => 'OK']));

// Маршрути лабораторної №5 залишені для сумісності з попередніми тестами.
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [LegacyProductController::class, 'index'])->name('index');
    Route::post('/', [LegacyProductController::class, 'store'])->middleware('store.client')->name('store');
    Route::get('/{id}', [LegacyProductController::class, 'show'])->whereNumber('id')->name('show');
});

Route::get('/catalog', [ProductController::class, 'index']);
Route::post('/catalog', [ProductController::class, 'store']);
Route::get('/catalog/{product}', [ProductController::class, 'show']);
Route::put('/catalog/{product}', [ProductController::class, 'update']);
Route::delete('/catalog/{product}', [ProductController::class, 'destroy']);

Route::apiResource('categories', CategoryController::class);
Route::apiResource('tags', TagController::class);

Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{user}', [UserController::class, 'show']);

Route::get('/carts/{cart}', [CartController::class, 'show']);
Route::post('/carts/{cart}/items', [CartController::class, 'addItem']);

Route::get('/orders', [OrderController::class, 'index']);
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{order}', [OrderController::class, 'show']);

Route::post('/orders/{order}/shipment', [ShipmentController::class, 'store']);
Route::get('/shipments/{shipment}', [ShipmentController::class, 'show']);
