<?php

use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\MailController;
use App\Http\Controllers\Api\TypeProjectController;
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
// Route::get('/projects', [ProjectController::class, 'index']);
// Le altre rotte che mi serviranno in futuro sono:
// Route::get('/projects/{project}', [ProjectController::class, 'show']);
// Route::post('/projects/{project}', [ProjectController::class, 'store']);
// Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);
// Route::put('/projects/{project}', [ProjectController::class, 'upadate']);

// Posso scrivere tutte le rotte manualmente oppure con: only mi permette di dirgli quali usare
Route::apiResource('/projects', ProjectController::class)->only('index');
Route::get('/projects/{slug}', [ProjectController::class, 'show']);

// Comando per vedere le rotte: php artisan route:list e posso filtrare con --path=api

// Creo la rotta per inviare le informazioni della email ricevuta
Route::post('/contact-mail', [MailController::class, 'message']);

// Rotta per i post legati ad una categoria
Route::get('/types/{id}/projects', TypeProjectController::class);
