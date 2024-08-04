<?php

use App\Http\Controllers\LineController;
use App\Http\Controllers\LoginHistoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return to_route('login_histories.create');
});

Route::get('/line/account-link/', [LineController::class, '/create'])->name('line.create');

Route::middleware(['login.verify'])->group(function(){    
    Route::controller(LoginHistoryController::class)->prefix('login_histories')->as('login_histories.')->group(function(){
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
    });
});
