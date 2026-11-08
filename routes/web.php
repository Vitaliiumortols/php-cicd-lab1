<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'time' => now()->toDateTimeString(),
    ]);
})->name('health');

Route::get('/system-info', [CatalogController::class, 'info'])
    ->name('system.info');

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])
        ->name('index');

    Route::post('/', [ProductController::class, 'store'])
        ->middleware('store.client')
        ->name('store');

    Route::get('/{id}', [ProductController::class, 'show'])
        ->whereNumber('id')
        ->name('show');
});
