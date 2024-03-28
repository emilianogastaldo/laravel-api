<?php

use App\Http\Controllers\Api\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Rotta per recuperare i progetti dal database con json 
// (ricordati che aggiunge in automatico api davanti a /projects)
Route::get('/projects', [ProjectController::class, 'index']);
// Le altre rotte che mi serviranno in futuro sono:
// Route::get('/projects/{project}', [ProjectController::class, 'show']);
// Route::post('/projects/{project}', [ProjectController::class, 'store']);
// Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);
// Route::put('/projects/{project}', [ProjectController::class, 'upadate']);

// Posso scrivere tutte le rotte manualmente oppure con:
// Route::apiResource('/projects', ProjectController::class);
