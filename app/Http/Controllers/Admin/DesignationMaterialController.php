<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DesignationMaterialCreateRequest;
use App\Http\Requests\DesignationMaterialUpdateRequest;
use App\Models\Department;
use App\Models\Designation;
use App\Models\DesignationMaterial;
use App\Models\Material;
use App\Models\ReportApplicationStatement;
use App\Models\Specification;
use App\Services\DesignationMaterial\DesignationMaterialService;
use Illuminate\Http\Request;

class DesignationMaterialController extends Controller
{
    public $route = 'designation-materials';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = DesignationMaterial::with('material','designation')
            ->orderBy('created_at','desc')
            ->paginate(25);

        return view('administrator::include.designation-material.index', [
            'route' => 'designation-materials',
            'livewire_search' => 'designation-material-search',
            'title' => 'Норми',
            'items' => $items
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //$last = Specification::orderBy('id','desc')->with('designations')->first();

        return view('administrator::include.designation-material.create', [
            //'lastDesignation' => $last,
            'departments' => Department::all(),
            'route' => $this->route]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, DesignationMaterialService $service)
    {
        //dd($request);
        $service->store($request);
        return redirect()->route($this->route.'.index')->with('status', 'Дані успішно збережено');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DesignationMaterial $designationMaterial)
    {
        return view('administrator::include.designation-material.edit', [
            //'lastDesignation' => $last,
            'designationMaterial' => $designationMaterial,
            'departments' => Department::all(),
            'route' => $this->route]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DesignationMaterialUpdateRequest $request, DesignationMaterial $designationMaterial, DesignationMaterialService $service)
    {
        $service->update($request, $designationMaterial);

        return redirect()->route($this->route.'.index')->with('status', 'Дані успішно збережено');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DesignationMaterial $designationMaterial)
    {
        $designationMaterial->delete();
        return redirect()->route($this->route.'.index')->with('status', 'Дані успішно збережено');
    }
}
