<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DesignationCreateRequest;
use App\Models\Designation;
use App\Models\TypeUnit;
use Illuminate\Http\Request;

class PI0Controller extends Controller
{
    public $route = 'pi0s';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('administrator::include.pi0s.index', [
            'route' => $this->route,
            'livewire_search' => 'pi0-search',
            'title' => 'ПІ0']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view('administrator::include.pi0s.create',[
            'units' => TypeUnit::all(),
            'route' => $this->route ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DesignationCreateRequest $request)
    {
        $designation = Designation::create([
            'designation' => $request->designation,
            'name' => $request->name,
            'gost' => $request->gost,
            'type' => 1,
            'type_unit_id' => $request->type_unit_id,
        ]);

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
    public function edit(Designation $pi0)
    {
        return view('administrator::include.pi0s.edit',[
            'item' => $pi0,
            'route' => $this->route,
            'units' => TypeUnit::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Designation $pi0)
    {
        $validatedData = $request->validate([
            'designation' => 'required|unique:designations,designation,' . $pi0->id,
        ], [
            'designation.unique' => 'Такий креслярський номер вже є у виробах.',
        ]);
        $pi0->designation = $request->designation;
        $pi0->name = $request->name;
        $pi0->gost = $request->gost;
        $pi0->type_unit_id = $request->type_unit_id;
        $pi0->save();
        return redirect()->route($this->route.'.index')->with('status', 'Дані успішно збережено');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
