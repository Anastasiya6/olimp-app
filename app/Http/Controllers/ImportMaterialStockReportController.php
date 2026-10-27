<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\ImportMaterial;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ImportMaterialStockReportController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'required|integer|exists:departments,id',
        ]);

        $department = Department::findOrFail($validated['department_id']);
        $materials = ImportMaterial::query()
            ->select(['id', 'code', 'article', 'name', 'type_unit_id'])
            ->with('unit:id,unit')
            ->whereHas('stocks', function ($query) use ($department) {
                $query->where('department_id', $department->id);
            })
            ->withSum(['stocks as report_balance' => function ($query) use ($department) {
                $query->where('department_id', $department->id);
            }], 'amount')
            ->having('report_balance', '<>', 0)
            ->orderBy('name')
            ->get();

        return Pdf::loadView('pdf.import-material-stock-report', [
            'department' => $department,
            'materials' => $materials,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape')
            ->stream('import-material-stock-'.$department->id.'.pdf');
    }
}
