<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SpecificationCreateRequest;
use App\Http\Requests\SpecificationRequest;
use App\Models\Designation;
use App\Models\Specification;
use App\Services\Specification\SpecificationService;
use Illuminate\Http\Request;

class SpecificationController extends Controller
{
    public $route = 'specifications';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $specifications = Specification::with('designations','designationEntry')
                            ->orderBy('created_at','desc')
                            ->paginate(25);
        return view('administrator::include.specifications.index', compact('specifications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //$last = Specification::orderBy('id','desc')->with('designations')->first();

        return view('administrator::include.specifications.create', [
           // 'lastDesignation' => $last,
            'route' => $this->route]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SpecificationCreateRequest $request,SpecificationService $service)
    {
        $service->store($request);

        return redirect()->route('specifications.index')->with('status', 'Дані успішно збережено');

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
    public function edit(Specification $specification)
    {
        return view('administrator::include.specifications.edit', compact('specification'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SpecificationRequest $request, Specification $specification)
    {
        $specification->quantity = $request->quantity;
        $specification->save();
        return redirect()->route('specifications.index')->with('status', 'Дані успішно збережено');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Specification $specification)
    {
        $specification->delete();
        return redirect()->route('specifications.index')->with('status', 'Дані успішно збережено');

    }
}
