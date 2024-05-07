<?php

namespace App\Observers;

use App\Models\Specification;
use App\Models\SpecificationLog;

class SpecificationObserver
{
    /**
     * Handle the Specification "created" event.
     */
    public function created(Specification $specification): void
    {

        $this->addLog($specification,'Додано у специфікацію');

    }

    /**
     * Handle the Specification "updated" event.
     */
    public function updated(Specification $specification): void
    {
        if($specification->isDirty('quantity')){

            $message = "Оновлена кількість. Було ".$specification->getOriginal('quantity')." Стало ".$specification->quantity;

        }
        if($specification->isDirty('category_code')){

            $message.= " Оновлено шифр приналежності. Було ".$specification->getOriginal('category_code')." Стало ".$specification->category_code;

        }
        if($message){
            $this->addLog($specification,$message);
        }
    }

    /**
     * Handle the Specification "deleted" event.
     */
    public function deleted(Specification $specification): void
    {
        $this->addLog($specification,'Видалено зі специфікації');
    }

    /**
     * Handle the Specification "restored" event.
     */
    public function restored(Specification $specification): void
    {
        //
    }

    /**
     * Handle the Specification "force deleted" event.
     */
    public function forceDeleted(Specification $specification): void
    {
        //
    }

    public function addLog($specification,$message)
    {

        SpecificationLog::create([
            'designation_id' => $specification->designation_id,
            'designation_entry_id' => $specification->designation_entry_id,
            'designation_number' => $specification->designations->designation,
            'detail_number' => $specification->designationEntry->designation,
            'designation' => $specification->designations->name,
            'detail' => $specification->designationEntry->name,
            'message' => $message
        ]);
    }
}
