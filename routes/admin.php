<?php

use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\Admin\DesignationMaterialController;
use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PI0Controller;
use App\Http\Controllers\Admin\Report\DetailSpecificationNormController;
use App\Http\Controllers\Admin\Report\EntryDetailController;
use App\Http\Controllers\Admin\Report\NotNormForMaterialController;
use App\Http\Controllers\Admin\Report\SpecificationNormController;
use App\Http\Controllers\Admin\ReportController;
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
})->name('home');*/

//Route::get('/', [IndexController::class, 'index'])->name('home');

Route::get('/', [IndexController::class, 'index'])->name('admin.home');

Route::resource('specifications', \App\Http\Controllers\Admin\SpecificationController::class);
Route::resource('designations', DesignationController::class);
Route::resource('pi0s', PI0Controller::class);

Route::post('/update-names', [DesignationController::class, 'updateNames'])->name('designations.update-names');
Route::get('/edit-names/{designations_array}',  [DesignationController::class, 'editNames'])->name('designations.edit-names');
Route::resource('materials', MaterialController::class);
Route::resource('designation-materials', DesignationMaterialController::class);
Route::resource('orders', OrderController::class);
Route::resource('reports', ReportController::class);

Route::get('application-statement', [\App\Http\Controllers\Admin\Report\ApplicationStatement::class,'applicationStatement'])->name('application.statement');
Route::get('specification-material-norm/{order_number}', [SpecificationNormController::class,'specificationNorm'])->name('specification.material');
Route::get('detail-specification-material-norm/{department}/{order_number}', [DetailSpecificationNormController::class,'detailSpecificationNorm'])->name('detail.specification.material');
Route::get('entry-detail/{department}/{order_number}', [EntryDetailController::class,'entryDetail'])->name('entry.detail');
Route::get('not-norm-for-material/{department}/{order_number}', [NotNormForMaterialController::class,'notNormForMaterial'])->name('not.norm.material');
