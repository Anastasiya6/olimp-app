<?php

use App\Http\Controllers\Admin\DeliveryNoteController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\Admin\DesignationMaterialController;
use App\Http\Controllers\Admin\GroupMaterialController;
use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\Admin\Logs\DesignationMaterialLogController;
use App\Http\Controllers\Admin\Logs\SpecificationLogController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\MaterialPurchaseController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderNameController;
use App\Http\Controllers\Admin\PI0Controller;
use App\Http\Controllers\Admin\PlanTaskController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\Report\ApplicationStatementController;
use App\Http\Controllers\Admin\Report\DeliveryNotePlanController;
use App\Http\Controllers\Admin\Report\DeliveryNoteDocumentController;

use App\Http\Controllers\Admin\Report\DeliveryNoteDesignationController;
use App\Http\Controllers\Admin\Report\DetailSpecificationNormController;
use App\Http\Controllers\Admin\Report\EntryDetailController;
use App\Http\Controllers\Admin\Report\EntryDetailDesignationController;
use App\Http\Controllers\Admin\Report\NotInApplicationStatement;
use App\Http\Controllers\Admin\Report\NotNormForMaterialController;
use App\Http\Controllers\Admin\Report\PlanTaskDetailSpecificationNormController;
use App\Http\Controllers\Admin\Report\PlanTaskSpecificationNormController;
use App\Http\Controllers\Admin\Report\ReportDepartmentList;
use App\Http\Controllers\Admin\Report\ReportTaskController;
use App\Http\Controllers\Admin\Report\ReportTaskNoMaterialController;
use App\Http\Controllers\Admin\Report\ReportWriteOffController;
use App\Http\Controllers\Admin\Report\ReportWriteOffNoMaterialController;
use App\Http\Controllers\Admin\Report\SpecificationNormController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\WriteOffController;
use App\Livewire\Login;
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

//Route::get('/login',Login::class)->name('login');

//Route::middleware('auth')->group(function () {

    Route::get('/', [IndexController::class, 'index'])->name('admin.home');
    Route::resource('specifications', \App\Http\Controllers\Admin\SpecificationController::class);
    Route::resource('designations', DesignationController::class);
    Route::resource('departments', DepartmentController::class);

   // Route::get('tasks}', [TaskController::class, 'index'])->name('tasks.index');
    //Route::resource('tasks', TaskController::class);
    //Route::get('tasks/{sender_department}/create', 'App\Http\Controllers\Admin\TaskController@create')->name('tasks.create');
    Route::get('tasks/{type}', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('tasks/{type}/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::get('tasks/{sender_department}/{type}/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::post('tasks', [TaskController::class, 'store'])->name('tasks.store');

    Route::resource('pi0s', PI0Controller::class);
    Route::resource('delivery-notes', \App\Http\Controllers\Admin\DeliveryNoteController::class);


    Route::resource('materials', MaterialController::class);
    Route::resource('designation-materials', DesignationMaterialController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('reports', ReportController::class);
    Route::resource('group-materials', GroupMaterialController::class);
    Route::resource('specification-logs', SpecificationLogController::class);
    Route::resource('designation-material-logs', DesignationMaterialLogController::class);

    Route::resource('plan-tasks', PlanTaskController::class);
    Route::get('plan-tasks/{order_name_id}/{sender_department}/{receiver_department}/create', 'App\Http\Controllers\Admin\PlanTaskController@create')->name('plan-tasks.create');
    Route::resource('write-offs', WriteOffController::class);

    Route::get('pi0-all', [PI0Controller::class,'pi0Pdf'])->name('pi0.all');
    Route::get('application-statement/{filter}/{order_name_id}/{department}', [ApplicationStatementController::class,'applicationStatement'])->name('application.statement');
    Route::get('specification-material-norm/{order_name_id}/{department}', [SpecificationNormController::class,'specificationNorm'])->name('specification.material');
    Route::get('detail-specification-material-norm/{order_name_id}/{department}', [DetailSpecificationNormController::class,'detailSpecificationNorm'])->name('detail.specification.material');
    Route::get('entry-detail/{order_name_id}', [EntryDetailController::class,'entryDetail'])->name('entry.detail');
    Route::get('entry-detail-designation/{designation_number?}/{department}', [EntryDetailDesignationController::class,'entryDetailDesignation'])->name('entry.detail.designation');
    Route::get('write-off/{ids}/{order_name_id}/{start_date}/{end_date}/{sender_department}/{receiver_department}/{type_report}', [ReportWriteOffController::class,'writeOff'])->name('report.write.off');
    Route::get('write-off-no-material/{order_name_id}/{start_date}/{end_date}/{sender_department}/{receiver_department}', [ReportWriteOffNoMaterialController::class,'writeOffNoMaterial'])->name('report.write.off.no.material');
    Route::get('not-in-application-statement/{sender_department}/{order_name_id}', [NotInApplicationStatement::class,'notInApplicationStatement'])->name('report.not.in.application.statement');
    Route::get('department-list/{filter}/{order_name_id}/{department}', [ReportDepartmentList::class,'reportDepartmentList'])->name('department.list');

    Route::post('task-detail', [ReportTaskController::class,'task'])->name('report.task.detail');
    Route::post('task-material', [ReportTaskController::class,'task'])->name('report.task.material');
    Route::post('task-no-material', [ReportTaskNoMaterialController::class,'task'])->name('report.task.no.material');
//Route::get('write-off/{items}', [ReportWriteOffController::class,'writeOff'])->name('report.write.off');
    Route::resource('order-names', OrderNameController::class);

    Route::resource('purchases', PurchaseController::class);
    Route::resource('material-purchases', MaterialPurchaseController::class);

    Route::get('plan-task-specification-norm/{order_name_id}/{sender_department}/{receiver_department}/{type_report_in}/{with_purchased}/{with_material_purchased}', [PlanTaskSpecificationNormController::class,'planTaskSpecificationNorm'])->name('plan-task.specification.norm');
    Route::get('plan-task-detail-specification-norm/{order_name_id}/{sender_department}/{receiver_department}/{type_report_in}/{with_purchased}/{with_material_purchased}', [PlanTaskDetailSpecificationNormController::class,'planTaskDetailSpecificationNorm'])->name('plan-task.detail.specification.norm');

    Route::get('plan-task-all/{order_name_id}/{sender_department}/{receiver_department}', [PlanTaskController::class,'planTaskPdf'])->name('plan-tasks.all');

    Route::get('plan-task-export/{order_name_id}/{department}', [PlanTaskController::class,'exportExcel'])->name('plan-tasks.export');


    Route::get('not-norm-for-material/{department}/{order_name_id}', [NotNormForMaterialController::class,'notNormForMaterial'])->name('not.norm.material');
    Route::get('delivery-note-plan/{sender_department}/{receiver_department}/{order_name_id}', [DeliveryNotePlanController::class,'deliveryNotePlan'])->name('delivery.notes.plan');
    Route::get('delivery-note/{sender_department}/{receiver_department}/{order_name_id}/{document_date}', [DeliveryNoteDocumentController::class,'deliveryNote'])->name('delivery.notes');

    Route::get('delivery-note/{designation}', [DeliveryNoteDesignationController::class,'deliveryNoteDesignation'])->name('delivery.notes.designation');
//});
