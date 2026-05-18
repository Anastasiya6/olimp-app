<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialCoefficientCreateRequest;
use App\Models\MaterialCoefficient;
use Illuminate\Http\Request;

class MaterialCoefficientController extends Controller
{
    public $route = 'material_coefficients';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('administrator::include.material_coefficients.index', [
            'items' => MaterialCoefficient::all(),
            'route' => $this->route,
            'title' => 'Коефіцієнти']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('administrator::include.material_coefficients.create',[
            'route' => $this->route ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MaterialCoefficientCreateRequest $request)
    {
        $typeUnit = new MaterialCoefficient();
        $typeUnit->keyword = $request->keyword;
        $typeUnit->coefficient = $request->coefficient;
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
    public function edit(MaterialCoefficient $materialCoefficient)
    {
        return view('administrator::include.material_coefficients.edit',[
            'item' => $materialCoefficient,
            'route' => $this->route
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MaterialCoefficientCreateRequest $request, MaterialCoefficient $materialCoefficient)
    {
        $materialCoefficient->keyword = $request->keyword;
        $materialCoefficient->coefficient = $request->coefficient;
        $materialCoefficient->save();
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
