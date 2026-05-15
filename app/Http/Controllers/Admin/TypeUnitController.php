<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TypeUnit;
use Illuminate\Http\Request;

class TypeUnitController extends Controller
{
    public $route = 'type_units';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('administrator::include.type_units.index', [
            'items' => TypeUnit::all(),
            'route' => $this->route,
            'title' => 'Цеха']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('administrator::include.type_units.create',[
            'route' => $this->route ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $typeUnit = new TypeUnit();
        $typeUnit->unit = $request->unit;
        $typeUnit->save();

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
    public function edit(TypeUnit $typeUnit)
    {
        return view('administrator::include.type_units.edit',[
            'item' => $typeUnit,
            'route' => $this->route
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TypeUnit $typeUnit)
    {
        $typeUnit->unit = $request->unit;
        $typeUnit->save();
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
