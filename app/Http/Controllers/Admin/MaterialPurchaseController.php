<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialPurchaseCreateRequest;
use App\Http\Requests\MaterialPurchaseUpdateRequest;
use App\Models\MaterialPurchase;
use App\Services\MaterialPurchase\MaterialPurchaseService;

class MaterialPurchaseController extends Controller
{
    public $route = 'material-purchases';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('administrator::include.material-purchases.index',[
            'title' => 'Заміна матеріалів',
            'route' => $this->route,
            'livewire_search' => 'material-purchase-search'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('administrator::include.material-purchases.create', [
            'route' => $this->route
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MaterialPurchaseCreateRequest $request, MaterialPurchaseService $service)
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
    public function edit(MaterialPurchase $material_purchase)
    {
        return view('administrator::include.material-purchases.edit', [
            'item' => $material_purchase,
            'route' => $this->route,
            'title' => 'Редагувати запис'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MaterialPurchaseUpdateRequest $request, MaterialPurchase $material_purchase, MaterialPurchaseService $service)
    {
        $service->update($request,$material_purchase);
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
