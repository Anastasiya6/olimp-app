<?php

namespace App\Observers;

use App\Models\Department;
use App\Models\DesignationMaterial;
use App\Models\DesignationMaterialLog;

class DesignationMaterialObserver
{
    /**
     * Handle the DesignationMaterial "created" event.
     */
    public function created(DesignationMaterial $designationMaterial): void
    {
        $this->addLog($designationMaterial,'Додано у норми');

    }

    /**
     * Handle the DesignationMaterial "updated" event.
     */
    public function updated(DesignationMaterial $designationMaterial): void
    {
        $message = '';

        if($designationMaterial->isDirty('norm')){

            $message = "Оновлена норма. Було ".$designationMaterial->getOriginal('norm')." Стало ".$designationMaterial->norm;

        }
        if($designationMaterial->isDirty('department_id')){
            $departmentId = $designationMaterial->getOriginal('department_id');

            $department = Department::find($departmentId);
            $departmentNumber = $department->number??"";

            $message.= "Змінено цех. Було ".$departmentNumber." Стало ".$designationMaterial->department->number;

        }
        if($message){
            $this->addLog($designationMaterial,$message);
        }
    }

    /**
     * Handle the DesignationMaterial "deleted" event.
     */
    public function deleted(DesignationMaterial $designationMaterial): void
    {
        $this->addLog($designationMaterial,'Видалено з норм');

    }

    /**
     * Handle the DesignationMaterial "restored" event.
     */
    public function restored(DesignationMaterial $designationMaterial): void
    {
        //
    }

    /**
     * Handle the DesignationMaterial "force deleted" event.
     */
    public function forceDeleted(DesignationMaterial $designationMaterial): void
    {
        //
    }
    public function addLog(DesignationMaterial $designationMaterial,$message)
    {
        DesignationMaterialLog::create([
            'designation_id' => $designationMaterial->designation_id,
            'designation_number' => $designationMaterial->designation->designation,
            'designation' => $designationMaterial->designation->name,
            'material_id' => $designationMaterial->material_id,
            'material' => $designationMaterial->material->name,
            'message' => $message
        ]);
    }
}
