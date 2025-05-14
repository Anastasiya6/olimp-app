<?php

namespace App\Services\DeliveryNote;
use App\Models\DeliveryNote;
use App\Models\Designation;
use App\Models\OrderName;
use App\Models\PlanTask;
use App\Services\HelpService\HelpService;
use Illuminate\Http\Request;

class DeliveryNoteService
{
    public function store(Request $request)
    {
        $designation = Designation::where('designation', $request->designation)->first();

        if(!isset($designation->id)){

            $designation = $this->createDesignation(
                                                    $request->designation,
                                                    $request->designation_name,
                  );
        }

        if($designation->id){
            DeliveryNote::create(
                [
                    'designation_id' => $designation->id,
                    'designation_number' => $designation->designation,
                    'designation_name' => $designation->name,
                    'quantity' => $request->quantity,
                    'document_number' => $request->document_number,
                    'document_date' => $request->document_date,
                    'order_name_id' => $request->order_name_id,
                    'sender_department_id' => $request->sender_department_id,
                    'receiver_department_id' => $request->receiver_department_id,
                    'with_purchased' => $request->with_purchased,
                    'with_material_purchased' => $request->with_material_purchased
                ]
            );

            $attributes = [
                'order_name_id' => $request->order_name_id,
                'designation_id' => $designation->id,
            ];

            $values = [
                'category_code' => 0,
                'quantity' => 0,
                'quantity_total' => 0,
                'sender_department_id' => $request->sender_department_id,
                'receiver_department_id' => $request->receiver_department_id,
                'order_designationEntry' =>HelpService::getNumbers($request->designation) ,
                'order_designationEntry_letters' => HelpService::getLetters($request->designation),
                'is_report_application_statement' => 2 // зі здаточних
           ];

            $plan_task =  PlanTask::firstOrCreate($attributes, $values);

            if ($plan_task->wasRecentlyCreated) {

                $order_name = OrderName::where('id',$request->order_name_id)->first()?->name;
                // Створено новий запис
                session()->flash('title', 'Додано у план ');
                session()->flash('message', 'Додано у план '.$request->designation.' у замовлення '.$order_name);
                session()->flash('show_modal', true);
            }
            return $plan_task;
        }
    }

    public function update(Request $request, DeliveryNote $deliveryNote)
    {
      //  dd($request);
        $deliveryNote->sender_department_id = $request->sender_department_id;
        $deliveryNote->document_number = $request->document_number;
        $deliveryNote->document_date = $request->document_date;
        $deliveryNote->receiver_department_id = $request->receiver_department_id;
        $deliveryNote->order_name_id = $request->order_name_id;
        $deliveryNote->quantity = $request->quantity;
        $deliveryNote->with_purchased = $request->with_purchased;
        $deliveryNote->with_material_purchased = $request->with_material_purchased;
        $deliveryNote->save();
    }

    private function createDesignation($designation,$name='')
    {

        return
            Designation::create([
                'designation' => $designation,
                'name' => $name,
            ]);
    }

}
