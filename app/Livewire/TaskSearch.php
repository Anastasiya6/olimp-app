<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Task;
use App\Services\HelpService\NoMaterialService;
use Illuminate\Http\Request;
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

    public $without_coefficient = 0;

    public $selectedItems = [];

    public $type;
    public ?Task $editingTask = null;
    public bool $taskFormLoaded = false;
    public int $taskFormVersion = 0;
    public $formDepartmentId;
    public $formDepartmentNumber;

    public function openTaskForm($id = null)
    {
        $this->editingTask = $id ? Task::with('designation')->where('type', $this->type)->findOrFail($id) : null;
        $this->formDepartmentId = $this->editingTask?->department_id ?? $this->selectedDepartmentSender;
        $this->formDepartmentNumber = Department::find($this->formDepartmentId)?->number ?? '';
        $this->taskFormLoaded = true;
        $this->taskFormVersion++;
        $this->resetValidation();
        $this->dispatch('task-form-open');
    }

    public function mount(Request $request)
    {
        $this->type = $request->type??0;

//        if($this->type == 'technologist'){
//
//            $this->without_coefficient = true;
//        }

        if(!$this->selectedDepartmentSender) {
            $this->selectedDepartmentSender = Department::DEFAULT_FIRST_DEPARTMENT_ID;
        }
        if (old('_task_form') && session()->has('errors') && old('type') === $this->type) {
            $this->selectedDepartmentSender = old('department_id', $this->selectedDepartmentSender);
            $this->openTaskForm(old('_task_edit') ?: null);
            $this->setErrorBag(session('errors')->getBag('default'));
        }
    }

    public function deleteTask($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
        if ($this->editingTask?->id === (int) $id) {
            $this->editingTask = null;
            $this->taskFormLoaded = false;
        }

        // Отправить сообщение об успешном удалении
        session()->flash('message', 'Запис успішно видалено.');
    }

    public function deleteAllTask($department_id)
    {
        $task = Task
            ::where('department_id',$department_id)
            ->where('type',$this->type)
            ->delete();

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
//        $this->without_coefficient = (int) $this->without_coefficient;

        $this->resetPage();
    }

    protected function tasks()
    {
        $items = Task
                ::where('department_id', $this->selectedDepartmentSender)
                ->where('type',$this->type)
                ->orderBy('updated_at','desc')
                ->get();
        $selected_department_number = Department
                                            ::where('id',$this->selectedDepartmentSender)
                                            ->first()
                                            ?->number;

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
