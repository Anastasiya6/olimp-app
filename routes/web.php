<?php

use App\Http\Controllers\Pub\Logs\DesignationMaterialLogController;
use App\Http\Controllers\Pub\Logs\SpecificationLogController;
use App\Http\Controllers\Pub\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Pub\IndexController;
use App\Http\Controllers\Pub\MakeApplicationStatement;
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

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/', [IndexController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/make-application-statement', [makeApplicationStatement::class, 'makeApplicationStatement'])->name('make.application.statement');
Route::resource('public-reports', ReportController::class);
Route::resource('public-specification-logs', SpecificationLogController::class);
Route::resource('public-designation-material-logs', DesignationMaterialLogController::class);
