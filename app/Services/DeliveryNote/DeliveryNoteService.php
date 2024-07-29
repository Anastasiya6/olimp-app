<?php

namespace App\Services\DeliveryNote;
use App\Models\DeliveryNote;
use App\Models\Designation;
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
                    'receiver_department_id' => $request->receiver_department_id

                ]
            );
        }
    }

    public function update(Request $request, DeliveryNote $deliveryNote)
    {
       // $deliveryNote->department_id = $request->department_id;
        $deliveryNote->quantity = $request->quantity;
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
