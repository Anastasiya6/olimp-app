<?php

namespace App\Http\Controllers\Pub\Logs;

use App\Http\Controllers\Controller;
use App\Models\DesignationMaterial;
use App\Models\DesignationMaterialLog;
use Illuminate\Http\Request;

class DesignationMaterialLogController extends Controller
{
    public $route = 'designation-material-logs';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('public::include.designation-material-logs.index', [
            'items' => DesignationMaterialLog::orderBy('updated_at','desc')->paginate(25),
            'route' => $this->route,
            'title' => 'Зміни у нормах']);
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
