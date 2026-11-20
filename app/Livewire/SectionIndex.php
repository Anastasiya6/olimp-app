<?php

namespace App\Livewire;

use App\Models\Section;
use Livewire\Component;

class SectionIndex extends Component
{
    public ?Section $editingSection = null;

    public int $editFormVersion = 0;

    public function mount()
    {
        if ((old('_section_create') || old('_section_edit')) && session()->has('errors')) {
            $this->setErrorBag(session('errors')->getBag('default'));
        }
        if (old('_section_edit')) {
            $this->editingSection = Section::find(old('_section_edit'));
        }
    }

    public function editSection($id)
    {
        $this->editingSection = Section::findOrFail($id);
        $this->editFormVersion++;
        $this->resetValidation();
        $this->dispatch('section-edit-open');
    }

    public function render()
    {
        return view('livewire.section-index', ['items' => Section::all()]);
    }
}
