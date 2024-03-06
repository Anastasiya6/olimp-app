<?php

namespace App\Services\Specification;
use App\Models\Designation;
use App\Models\Specification;
use App\Models\Ticker\PostStatus;
use Illuminate\Http\Request;
use App\Models\Publication\Publication;
use Illuminate\Database\Eloquent\Builder;

class SpecificationService
{

    public function store(Request $request)
    {
        $designationsArray = array();

        $designation1 = Designation::where('designation', $request->designation_entry_designation)->first();
        $designation2 = Designation::where('designation', $request->designation_designation)->first();

        if(!isset($designation1->id)){

            $designation1 = $this->createDesignation($request->designation_entry_designation,$request->designation_entry_designation_name);

            $designationsArray[] = $designation1->id;

        }

        if(!isset($designation2->id)){

            $designation2 = $this->createDesignation($request->designation_designation);

            $designationsArray[] = $designation2->id;

        }

        if($designation1->id && $designation2->id){

            Specification::firstOrCreate([
                'designation' => $request->designation_designation,
                'detail' => $request->designation_entry_designation,
                'quantity' => $request->specification_quantity,
                'category_code' => $request->specification_category_code,
                'designation_id' => $designation2->id,
                'designation_entry_id' => $designation1->id,
            ]);
        }
    }

    private function createDesignation($designation,$name='')
    {
        return
            Designation::create([
                'designation' => $designation,
                'name' => $name
            ]);
    }

}
