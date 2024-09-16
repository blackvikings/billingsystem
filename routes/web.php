<?php

use App\Models\Item;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

Route::get('/', function () {
    $items = Item::all();
    return view('welcome', compact('items'));
});

Route::get('get-items', [ItemController::class, 'index'])->name('get-items');
Route::get('get-item/{id}', [ItemController::class, 'getItem'])->name('get-item');
Route::post('save-order', [ItemController::class, 'store'])->name('save-order');
