<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
//use App\Http\Requests\DesignationCreateRequest;
use App\Http\Requests\SectionCreateRequest;
use App\Models\Department;
use App\Models\Section;
use App\Models\User;
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
            'departments' => Department::orderBy('number')->get(['id', 'number']),
            'users' => User::orderBy('name')->get(['id', 'name']),
            'route' => $this->route ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SectionCreateRequest $request)
    {
        $section = new Section();
        $section->number = $request->number;
        $section->chief_user_id = $request->validated('chief_user_id');
        $section->department_id = $request->validated('department_id');
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
            'departments' => Department::orderBy('number')->get(['id', 'number']),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'chief_user_id' => ['required', 'integer', 'exists:users,id'],
        ], [], ['department_id' => 'цех', 'chief_user_id' => 'начальник ділянки']);

        $section->number = $request->number;
        $section->chief_user_id = $validated['chief_user_id'];
        $section->department_id = $validated['department_id'];
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
