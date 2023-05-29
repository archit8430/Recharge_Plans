<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\apiController as api;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Proteced API with token
Route::middleware('auth:sanctum')->group(function() {
    //Company CRUD API
    Route::prefix('/company')->group(function () {
        Route::get('',[api::class,'listCompany']);
        Route::post('',[api::class,'updateCompany']);
        Route::put('',[api::class,'updateCompany']);
        Route::delete('',[api::class,'deleteCompany']);
    });

    //Commission CRUD API
    Route::prefix('/commission')->group(function () {
        Route::get('',[api::class,'listCommission']);
        Route::post('',[api::class,'updateCommission']);
        Route::put('',[api::class,'updateCommission']);
        Route::delete('',[api::class,'deleteCommission']);
    });
    
    //Recharge API
    Route::prefix('/recharge')->group(function () {
        Route::post('',[api::class,'insertRechargeData']);
        Route::post('status',[api::class,'updateRechargeStatus']);
    
    });

    //Reports Api
    Route::post('getSummaryData',[api::class,'getSummaryData'])->name('summaryData');
    Route::post('getCompanySalesData',[api::class,'getCompanySalesData'])->name('companyWiseReport');
    Route::post('getSalesReport',[api::class,'getSalesReport'])->name('companySalesReport');
    Route::post('getCommReport',[api::class,'getCommReport'])->name('commissionReport');
    Route::post('downloadReport',[api::class,'downloadReport'])->name('downloadReport');

    //Logout Api
    Route::get('logoutUser',[api::class,'logout']);
    
});


Route::post('registerUser',[api::class,'RegisterUser']);
Route::post('loginUser',[api::class,'loginUser']);