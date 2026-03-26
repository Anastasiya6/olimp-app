<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaterialIssue;
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
