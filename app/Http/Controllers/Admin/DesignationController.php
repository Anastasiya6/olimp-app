<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DesignationController extends Controller
{
    public $route = 'designations';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('administrator::include.designations.index', [
                'route' => 'designations',
                'livewire_search' => 'designation-material-search',
                'title' => 'Вироби']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('administrator::include.designations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $designation = Designation::firstOrCreate([
            'designation' => $request->designation,
        ], [
            'name' => $request->name,
            'route' => $request->route,
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
    public function edit(Designation $designation)
    {
        return view('administrator::include.designations.edit', compact('designation'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Designation $designation)
    {
        $designation->designation = $request->designation;
        $designation->name = $request->name;
        $designation->route = $request->route;
        $designation->save();
        return redirect()->route($this->route.'.index')->with('status', 'Дані успішно збережено');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editNames(string $designations_array)
    {
        $designations = Designation::whereIn('id', explode(',',$designations_array))->get();
        //dd($designations);
        return view('administrator::include.designations.editNames', compact('designations_array','designations'));
    }

    public function updateNames(Request $request)
    {
        //dd($request->designations);

        foreach ($request->designations as $id => $designation){
          //  dd($designation);
            DB::table('designations')
                ->where('id', $id)
                ->update([
                    'designation' => $designation['designation'],
                    'name' => $designation['name'],
                    'route' => $designation['route']
                ]);
        }
        return redirect()->route('specifications.index')->with('status', 'Дані успішно збережено');


    }

}
