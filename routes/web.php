<?php

use App\Http\Controllers\Admin\HomeController as AdminHomeController;
use App\Http\Controllers\Guest\HomeController as GuestHomeControler;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Guest\ProjectController as GuestProjectController;
use App\Http\Controllers\ProfileController;
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

Route::get('/', GuestHomeControler::class)->name('guest.home');

Route::get('/projects/{project}', [GuestProjectController::class, 'show'])->name('guest.projects.show');

Route::prefix('/admin')->name('admin.')->middleware('auth')->group(function () {
    // Rotta home admin
    Route::get('', AdminHomeController::class)->middleware('auth')->name('home');

    // Rotta projects admin
    Route::resource('projects', AdminProjectController::class);

    // Che è come fare queste 7 righe:
    // Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    // Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    // Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    // Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    // Route::get('/projects/{post}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    // Route::put('/projects/{post}', [ProjectController::class, 'update'])->name('projects.update');
    // Route::delete('/projects/{post}', [ProjectController::class, 'destroy'])->name('projects.destroy');
});


// Rotte profilo
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
