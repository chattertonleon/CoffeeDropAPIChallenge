<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CashbackController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('locations',LocationController::class);

Route::get('/GetNearestLocation',[LocationController::class,'getByPostcode']);

Route::post('/CreateNewLocation',[LocationController::class,'createNewLocation']);

Route::post('/CalculateCashback',[CashbackController::class,'getCashback']);

Route::get('/MostRecentCashback',[CashbackController::class,'getMostRecentCashbacks']);
