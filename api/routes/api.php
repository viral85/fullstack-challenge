<?php

use App\Http\Controllers\WeatherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return response()->json([
//         'message' => 'all systems are a go',
//         'users' => \App\Models\User::all(),
//     ]);
// });
Route::get('/', [WeatherController::class, 'getUserWeather']);
Route::get('/get-weather/{id?}', [WeatherController::class, 'getWeather']);
