<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\MaterialIssuance;
use App\Models\MaterialIssue;
use App\Models\OrderName;
use App\Services\HelpService\MaterialService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MaterialIssueController extends Controller
{
    public $route = 'material-issue';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = MaterialIssue::with('material','designation')
            ->orderBy('created_at','desc')
            ->paginate(25);

        return view('administrator::include.material-issues.index', [
            'route' => $this->route,
            'livewire_search' => 'material-issue-search',
            'title' => 'Видача матеріалів',
            'items' => $items
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('administrator::include.material-issues.create', [
            'route' => $this->route]);
    }

    /**
     * Store a newly created resource in storage.
     */
//    public function store(Request $request, DesignationMaterialService $service)
//    {
//        $service->store($request);
//        return redirect()->route($this->route.'.index')->with('status', 'Дані успішно збережено');
//
//    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function materialIssuePdf($order_name_id,$designation_number,MaterialService $materialService)
    {

        $designation = Designation::where('designation', $designation_number)->first();
        $order = OrderName::find($order_name_id);

        if($designation && $order_name_id){
            $materialIssuance = MaterialIssuance::with( 'items.material', 'items.importMaterial')->where('order_name_id', $order_name_id)->where('plan_task_designation_id', $designation->id)->get();
            $result = [
                'material_id' => [],
                'designation_id' => [],
            ];

            foreach ($materialIssuance as $document) {
                foreach ($document->items as $item) {

                    if ($item->designation_id) {
                        $result['designation_id'][$item->designation_id][$item->details][] = [
                            'item' => $item,
                            'quantity' => $document->quantity,
                        ];
                    } elseif ($item->material_id) {
                        $result['material_id'][$item->material_id][$item->details][] = [
                            'item' => $item,
                            'quantity' => $document->quantity,
                        ];
                    }
                }
            }
            $record = clone $materialIssuance->first();
            $record->designation_id = $record->plan_task_designation_id;

            $records = collect([$record]);
            $all_materials = $materialService->material($records,1,5,'material_id');

            $pdf = Pdf::loadView('pdf.material-issue', [
                'all_materials' => $all_materials,
                'result' => $result,
                'designation' => $designation->designation,
                'order' => $order->name,
                'order_quantity' => $order->quantity
            ]);

            return $pdf->stream("material-issue.pdf");
        }


    }
    /**
     * Show the form for editing the specified resource.
     */
//    public function edit(DesignationMaterial $designationMaterial)
//    {
//        return view('administrator::include.material-issues.edit', [
//            //'lastDesignation' => $last,
//            'designationMaterial' => $designationMaterial,
//            'departments' => Department::all(),
//            'route' => $this->route]);
//    }

    /**
     * Update the specified resource in storage.
     */
//    public function update(DesignationMaterialUpdateRequest $request, DesignationMaterial $designationMaterial, DesignationMaterialService $service)
//    {
//        $service->update($request, $designationMaterial);
//
//        return redirect()->route($this->route.'.index')->with('status', 'Дані успішно збережено');
//    }

    /**
     * Remove the specified resource from storage.
     */
//    public function destroy(DesignationMaterial $designationMaterial)
//    {
//        $designationMaterial->delete();
//
//        return redirect()->route($this->route.'.index')->with('status', 'Дані успішно збережено');
//    }
}
