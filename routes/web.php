<?php

use App\Http\Controllers\LoginHistoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return to_route('login_histories.create');
});

Route::controller(UserController::class)->prefix('users')->as('users.')->group(function(){

});

Route::controller(LoginHistoryController::class)->prefix('login_histories')->as('login_histories.')->group(function(){
    Route::get('/create', 'create')->name('create');
    Route::post('/store', 'store')->name('store');
});
