<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\WizardStatsController;

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

Route::prefix('wizard-stats')->group(function () {
    Route::get('/', [WizardStatsController::class, 'index']);
    Route::get('/migrations', [WizardStatsController::class, 'getMigrations']);
    Route::get('/migrations/save', [WizardStatsController::class, 'save']);
});
