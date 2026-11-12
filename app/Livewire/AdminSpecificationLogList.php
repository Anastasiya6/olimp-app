<?php

namespace App\Livewire;

use App\Models\SpecificationLog;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AdminSpecificationLogList extends Component
{
    use WithPagination;

    #[Url(as: 'detail_name', except: '')]
    public string $detailName = '';

    public function updatedDetailName(): void
    {
        $this->resetPage();
    }

    public function resetSearch(): void
    {
        $this->reset('detailName');
        $this->resetPage();
    }

    public function render()
    {
        $search = trim($this->detailName);

        return view('livewire.admin-specification-log-list', [
            'items' => SpecificationLog::when($search !== '', fn ($query) =>
                $query->where('detail', 'like', '%'.$search.'%')
            )->orderByDesc('updated_at')->orderByDesc('id')->paginate(25),
        ]);
    }
}
