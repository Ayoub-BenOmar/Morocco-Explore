<?php

use Illuminate\Http\Request;
use App\Http\Controllers\auth;
use Illuminate\Support\Facades\Route;
use PharIo\Manifest\AuthorCollection;
use App\Http\Controllers\ItineraireController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/login', [auth::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/itineraries', [ItineraireController::class, 'store']);
    Route::put('/itineraries/{id}', [ItineraireController::class, 'update']);
    Route::post('/itineraries/{itineraryId}/add-to-visit-list', [ItineraireController::class, 'addToVisitList']);
});

Route::apiResource('auth', auth::class);
