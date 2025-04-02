<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Task;
use App\Services\HelpService\NoMaterialService;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Session;
use Livewire\Component;
use Livewire\WithPagination;

class TaskSearch extends Component
{
    use WithPagination;

    #[Session]
    public $selectedDepartmentSender;

    public $route = 'tasks';

    public $selectedDetails = [];

    public $selectedItems = [];

    public function mount()
    {
        if(!$this->selectedDepartmentSender) {
            $this->selectedDepartmentSender = Department::DEFAULT_FIRST_DEPARTMENT_ID;
        }
    }

    public function deleteTask($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        // Отправить сообщение об успешном удалении
        session()->flash('message', 'Запис успішно видалено.');
    }

    public function deleteAllTask($department_id)
    {
        $task = Task::where('department_id',$department_id)->delete();

        // Отправить сообщение об успешном удалении
        session()->flash('message', 'Запис успішно видалено.');
    }

    public function viewConfirm()
    {
        $this->flag = 1;

        $this->selectedDetails = Task::whereIn('id',$this->selectedItems)->get();

        $this->dispatch('open-modal',name:'viewLog');

    }

    public function updateSearch()
    {
        $this->resetPage();
    }

    protected function tasks()
    {
        $items = Task
                ::where('department_id', $this->selectedDepartmentSender)
                ->orderBy('updated_at','desc')
                ->get();
        $selected_department_number = Department::where('id',$this->selectedDepartmentSender)->first()?->number;

        foreach($items as $item) {

            $item->material = 1;

            if ($item->designationMaterial->isEmpty()) {
                $item->material = 0;
            }
            $item->material = NoMaterialService::noMaterial($item->designation_id, $item->designationMaterial->isNotEmpty(), 1, $selected_department_number);

            $item->designationName = $item->material['designation_entry_id'];
            if($item->material['status'] == 1 /*&& $this->flag == 0*/){

                $this->selectedItems[] = $item->id;
            }elseif($item->designationName ){
                $name = Designation::where('id',$item->designationName)->first();
                $item->designationName = $name->designation??"";//$item->designation->name;
            }

            $item->material = $item->material['status'];
        }
        return $items;

    }

    public function render()
    {
        return view('livewire.task-search',[
            'default_first_department' => Department::DEFAULT_FIRST_DEPARTMENT_ID,
            'departments' => Department::all(),
            'items'=>$this->tasks(),
        ]);
    }
}
