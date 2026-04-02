<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class UserSearch extends Component
{
    public $title = 'Користувачі';

    public $route = 'users';

    public function render()
    {

        return view('livewire.user-search', [
            'items' => User::orderBy('created_at')->paginate(25),
        ]);
    }
}
