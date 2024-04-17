<?php

namespace App\Services\Specification;
use App\Models\Designation;
use App\Models\Specification;
use Illuminate\Http\Request;

class SpecificationService
{
    public function store(Request $request)
    {
        $designationsArray = array();

        $designation1 = Designation::where('designation', $request->designation_entry_designation)->first();
        $designation2 = Designation::where('designation', $request->designation_designation)->first();

        if(!isset($designation1->id)){

            $designation1 = $this->createDesignation(
                                                    $request->designation_entry_designation,
                                                    $request->designation_entry_designation_name,
                                                    $request->type,
                                                    $request->designation_entry_route,
                                                    $request->designation_entry_gost,
                                                    $request->type_unit_id);

            $designationsArray[] = $designation1->id;

        }

        if(!isset($designation2->id)){

            $designation2 = $this->createDesignation(
                                                    $request->designation_designation,
                                                    $request->designation_name,
                                                    $request->type,
                                                    $request->designation_route,
                                                    $request->type_unit_id);

            $designationsArray[] = $designation2->id;

        }

        if($designation1->id && $designation2->id){

            Specification::updateOrCreate(
                [
                    'designation_id' => $designation2->id,
                    'designation_entry_id' => $designation1->id,
                ],
                [
                    'designation' => $request->designation_designation,
                    'detail' => $request->designation_entry_designation,
                    'quantity' => $request->specification_quantity,
                    'category_code' => $request->specification_category_code,
                ]
            );
        }
    }

    private function createDesignation($designation,$name='',$type,$route='',$gost='',$type_unit_id='')
    {

        return
            Designation::create([
                'designation' => $designation,
                'name' => $name,
                'route' => $route,
                'gost' => $gost,
                'type' => $type,
                'type_unit_id' => $type_unit_id
            ]);
    }

}
