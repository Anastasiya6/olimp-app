<?php

namespace App\Http\Controllers\Admin\Logs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SpecificationLogController extends Controller
{
    public $route = 'specification-logs';
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
         $request->validate(['detail_name' => ['nullable', 'string', 'max:255']]);
         $detailName = trim((string) $request->input('detail_name', ''));

         return view('administrator::include.specification-logs.index', [
            'detailName' => $detailName,
            'route' => $this->route,
            'title' => 'Зміни у специфікації']);
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
