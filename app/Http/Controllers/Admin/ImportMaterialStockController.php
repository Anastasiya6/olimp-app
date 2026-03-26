<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeliveryNoteRequest;
use App\Models\DeliveryNote;
use App\Models\Department;
use App\Models\ImportMaterial;
use App\Models\OrderName;
use App\Services\ImportMaterialStock\ImportMaterialStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ImportMaterialStockController extends Controller
{
    public $route = 'import-material-stocks';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('administrator::include.import-material-stocks.index', [
            'route' => $this->route,
            'livewire_search' => 'import-material-stock-search',
            'title' => 'Material from 1C'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('administrator::include.delivery-notes.create', [
            'departments' => Department::all(),
            'order_names' => OrderName::where('is_order',1)->orderBy('name')->get(),
            'last_record' => DeliveryNote::orderBy('updated_at','desc')->firstOrNew([]),
            'route' => $this->route]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDeliveryNoteRequest $request, ImportMaterialStock $service)
    {


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
    public function edit(DeliveryNote $deliveryNote)
    {
        $orders = OrderName::where('is_order',1)->orderBy('name')->get();

        return view('administrator::include.delivery-notes.edit',[
            'route' => $this->route,
            'departments' => Department::all(),
            'item' => $deliveryNote,
            'order_names' => $orders
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DeliveryNote $deliveryNote, ImportMaterialStock $service)
    {
        $service->update($request,$deliveryNote);
        return redirect()->route($this->route.'.index')->with('status', 'Дані успішно збережено');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function search(Request $request)
    {
        Log::info('ddd');
        return ImportMaterial::query()
            ->where('name', 'like', "%{$request->q}%")
            ->limit(10)
            ->get()
            ->map(function ($material) {
                $balance = ImportMaterialStock::where('import_material_id', $material->id)
                    ->sum('amount');

                return [
                    'id'       => $material->id,
                    'text'     => "{$material->name} | Залишок: {$balance}",
                    'balance'  => $balance,
                ];
            });
    }
}
