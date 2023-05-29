<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\userController;
use App\Http\Controllers\dashBoardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route that can be accessed without authentication
Route::middleware('guest')->group(function(){
    Route::get('/',[userController::class,'home'])->name('home');
    Route::get('login',[userController::class,'login'])->name('login');
    Route::post('login',[userController::class,'loginUser'])->name('loginUser');
    Route::get('register',[userController::class,'register'])->name('register');
    Route::post('register',[userController::class,'registerUser'])->name('registerUser');
});

//Route that can be accessed only with authentication
Route::middleware('auth')->group(function(){
    Route::get('logout',[userController::class,'logout'])->name('logout');
    Route::get('dashboard',[dashBoardController::class,'dashboard'])->name('dashboard');
    Route::get('companyReport',[dashBoardController::class,'companyReport'])->name('companyWiseSalesReport');
    Route::get('salesReport',[dashBoardController::class,'salesReport'])->name('salesReport');
    Route::get('commReport',[dashBoardController::class,'commReport'])->name('commReport');

});