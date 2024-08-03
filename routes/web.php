<?php

use App\Http\Controllers\LoginHistoryController;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return to_route('login_histories.create');
});

Route::controller(LoginHistoryController::class)->prefix('login_histories')->as('login_histories.')->group(function(){
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
});
