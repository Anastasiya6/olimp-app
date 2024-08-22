<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseCreateRequest;
use App\Http\Requests\PurchaseUpdateRequest;
use App\Models\Purchase;
use App\Services\Purchase\PurchaseService;

class PurchaseController extends Controller
{
    public $route = 'purchases';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('administrator::include.purchases.index',[
            'title' => 'Покупні',
            'route' => $this->route,
            'livewire_search' => 'purchase-search'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('administrator::include.purchases.create', [
            'route' => $this->route
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PurchaseCreateRequest $request, PurchaseService $service)
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
    public function edit(Purchase $purchase)
    {
        return view('administrator::include.purchases.edit', [
            'item' => $purchase,
            'route' => $this->route,
            'title' => 'Редагувати запис'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PurchaseUpdateRequest $request, Purchase $purchase, PurchaseService $service)
    {
        $service->update($request,$purchase);
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
