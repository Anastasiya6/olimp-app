<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialCreateRequest;
use App\Models\Material;
use App\Models\TypeUnit;
use App\Services\Material\DesignationMaterialService;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public $route = 'materials';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('administrator::include.materials.index', [
                    'route' => $this->route,
                    'livewire_search' => 'material-search',
                    'title' => 'Material']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('administrator::include.materials.create',[
            'units' => TypeUnit::all(),
            'route' => $this->route ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MaterialCreateRequest $request, DesignationMaterialService $service)
    {
        $service->store($request);

        return redirect()->route('materials.index')->with('status', 'Дані успішно збережено');
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
    public function edit(Material $material)
    {
        return view('administrator::include.materials.edit', [
            'material' => $material,
            'units' => TypeUnit::all(),
            'route' => $this->route

        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MaterialCreateRequest $request, Material $material, DesignationMaterialService $service)
    {
        //dd($request);
        $service->update($request, $material);

        return redirect()->route('materials.index')->with('status', 'Дані успішно збережено');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
