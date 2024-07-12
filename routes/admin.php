<?php

use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\Admin\DesignationMaterialController;
use App\Http\Controllers\Admin\DesignationTreeController;
use App\Http\Controllers\Admin\GroupMaterialController;
use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\Admin\Logs\DesignationMaterialLogController;
use App\Http\Controllers\Admin\Logs\SpecificationLogController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PI0Controller;
use App\Http\Controllers\Admin\PlanTaskController;
use App\Http\Controllers\Admin\Report\ApplicationStatementController;
use App\Http\Controllers\Admin\Report\DeliveryNoteController;
use App\Http\Controllers\Admin\Report\DetailSpecificationNormController;
use App\Http\Controllers\Admin\Report\EntryDetailController;
use App\Http\Controllers\Admin\Report\EntryDetailDesignationController;
use App\Http\Controllers\Admin\Report\NotNormForMaterialController;
use App\Http\Controllers\Admin\Report\SpecificationNormController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\WriteOffController;
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
Route::resource('delivery-notes', \App\Http\Controllers\Admin\DeliveryNoteController::class);


Route::resource('materials', MaterialController::class);
Route::resource('designation-materials', DesignationMaterialController::class);
Route::resource('orders', OrderController::class);
Route::resource('reports', ReportController::class);
Route::resource('group-materials', GroupMaterialController::class);
Route::resource('designation-trees', DesignationTreeController::class);
Route::resource('specification-logs', SpecificationLogController::class);
Route::resource('designation-material-logs', DesignationMaterialLogController::class);

Route::resource('plan-tasks', PlanTaskController::class);
Route::get('plan-tasks/{order_number}/create', 'App\Http\Controllers\Admin\PlanTaskController@create')->name('plan-tasks.create');
Route::get('plan-tasks/{planTask}/{order_number}/edit', 'App\Http\Controllers\Admin\PlanTaskController@edit')->name('plan-tasks.edit');
Route::resource('write-offs', WriteOffController::class);

Route::get('pi0-all', [PI0Controller::class,'pi0Pdf'])->name('pi0.all');
Route::get('application-statement/{filter}/{order_number}', [ApplicationStatementController::class,'applicationStatement'])->name('application.statement');
Route::get('specification-material-norm/{order_number}/{department}', [SpecificationNormController::class,'specificationNorm'])->name('specification.material');
Route::get('detail-specification-material-norm/{department}/{order_number}', [DetailSpecificationNormController::class,'detailSpecificationNorm'])->name('detail.specification.material');
Route::get('entry-detail/{order_number}', [EntryDetailController::class,'entryDetail'])->name('entry.detail');
Route::get('entry-detail-designation/{designation_number}/{department}', [EntryDetailDesignationController::class,'entryDetailDesignation'])->name('entry.detail.designation');

Route::get('not-norm-for-material/{department}/{order_number}', [NotNormForMaterialController::class,'notNormForMaterial'])->name('not.norm.material');
Route::get('delivery-note/{department}/{order_number}', [DeliveryNoteController::class,'deliveryNote'])->name('delivery.notes');
