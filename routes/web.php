<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PublicTransactionController;

Route::get('/keu', [PublicTransactionController::class, 'create'])
    ->name('public.transactions.create');

Route::post('/keu', [PublicTransactionController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('public.transactions.store');
