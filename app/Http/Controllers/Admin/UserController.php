<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

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
        User::create($this->validatedEmployee($request));

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
        $user->fill($this->validatedEmployee($request, $user))->save();
        return redirect()->route($this->route.'.index')->with('status', 'Дані успішно збережено');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    private function validatedEmployee(Request $request, ?User $user = null): array
    {
        if (is_string($request->input('login'))) {
            $request->merge(['login' => strtolower(trim($request->input('login'))) ?: null]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'login' => ['nullable', 'required_with:password', 'string', 'max:64',
                'regex:/^[a-z0-9_.-]+$/', Rule::unique('users', 'login')->ignore($user?->id)],
            'password' => [Rule::requiredIf(fn () => $request->filled('login') && empty($user?->password)),
                'nullable', 'string', 'confirmed', Password::defaults()],
        ], [
            'login.unique' => 'Цей логін уже використовується.',
            'login.regex' => 'Логін може містити латинські літери, цифри, крапку, дефіс і підкреслення.',
            'login.required_with' => 'Для входу з паролем вкажіть логін.',
            'password.required' => 'Для нового облікового запису вкажіть пароль.',
            'password.confirmed' => 'Підтвердження пароля не збігається.',
        ]);

        if (!$request->filled('password')) {
            unset($data['password']);
        }

        return $data;
    }
}
