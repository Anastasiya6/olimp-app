<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryNote;
use App\Models\Order;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;

class WriteOffController extends Controller
{
    public $route = 'orders';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('administrator::include.write-off.index', [
            'items' => DeliveryNote::orderBy('updated_at','desc')->paginate(25),
            'route' => $this->route,
            'livewire_search' => 'write-off-search',
            'title' => 'Списання']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
