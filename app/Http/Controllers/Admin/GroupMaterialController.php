<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GroupMaterial;
use App\Services\GroupMt\GroupMaterialService;
use Illuminate\Http\Request;

class GroupMaterialController extends Controller
{
    public $route = 'group-materials';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('administrator::include.group-materials.index', [
            'route' => $this->route,
            'livewire_search' => 'group-material-search',
            'title' => 'Матеріалокомплекти']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('administrator::include.group-materials.create',[
            'route' => $this->route ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, GroupMaterialService $service)
    {
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
    public function edit(GroupMaterial $groupMaterial)
    {
        return view('administrator::include.group-materials.edit', [
            'item' => $groupMaterial,
            'route' => $this->route

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GroupMaterial $groupMaterial, GroupMaterialService $service)
    {
        $service->update($request, $groupMaterial);

        return redirect()->route($this->route.'.index')->with('status', 'Дані успішно збережено');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GroupMaterial $groupMaterial)
    {
        $groupMaterial->delete();

        return redirect()->route($this->route.'.index')->with('status', 'Дані успішно збережено');
    }
}
