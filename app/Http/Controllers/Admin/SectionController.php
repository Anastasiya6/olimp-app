<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
//use App\Http\Requests\DesignationCreateRequest;
use App\Http\Requests\SectionCreateRequest;
use App\Models\Section;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{
    public $route = 'sections';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('administrator::include.sections.index', [
                'items' => Section::all(),
                'route' => $this->route,
                'title' => 'Ділянка']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('administrator::include.sections.create',[
            'route' => $this->route ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SectionCreateRequest $request)
    {
        $section = new Section();
        $section->number = $request->number;
        $section->chief_last_name = $request->chief_last_name;
        $section->save();

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
    public function edit(Section $section)
    {
        return view('administrator::include.sections.edit',[
            'item' => $section,
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Section $section)
    {
        $section->number = $request->number;
        $section->chief_last_name = $request->chief_last_name;
        $section->save();
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
