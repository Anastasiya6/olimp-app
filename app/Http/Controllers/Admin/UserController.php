<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public $route = 'users';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('administrator::include.users.index', [
            'title' => 'Користувачі',
            'route' => $this->route,
           // 'livewire_search' => 'user-search',
            'items' => User::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('administrator::include.users.create',[
            'route' => $this->route ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user= new User();
        $user->name = $request->name;
        $user->save();

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
    public function edit(User $user)
    {
        return view('administrator::include.users.edit',[
            'item' => $user,
            'route' => $this->route
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $user->name = $request->name;
        $user->save();
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
