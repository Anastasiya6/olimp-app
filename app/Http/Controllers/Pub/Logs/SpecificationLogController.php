<?php

namespace App\Http\Controllers\Pub\Logs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SpecificationLogController extends Controller
{
    public $route = 'specification-logs';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('public::include.specification-logs.index', [
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
