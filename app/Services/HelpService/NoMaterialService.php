<?php

namespace App\Services\HelpService;

use App\Models\Specification;
use App\Models\Test;

class NoMaterialService
{
    public static function noMaterial($designation_id,$material,$type=0,$sender_department_number=0): int|array
    {
        $specifications = Specification
            ::where('designation_id', $designation_id)
            ->with(['designations', 'designationEntry', 'designationMaterial'])
            ->get();
        $test= new Test();
        $test->designation_id = $designation_id;
        $test->material = $material;
        $test->type = $type;
        $test->route = $sender_department_number;
        $test->comment = $specifications->isNotEmpty() ? 1 : 0;
        $test->save();

        if ($specifications->isNotEmpty()) {
            foreach ($specifications as $specification) {

                if (str_starts_with($specification->designationEntry->designation, 'КР') || str_starts_with($specification->designationEntry->designation, 'ПИ0')) {
                    continue;
                }
                $tm = StatementService::getTm($specification);

                $route = SpecificationService::getRoute($specification,$tm,$sender_department_number);
               // $route = substr($specification->designationEntry->route, 0, 2);

                if($route == $sender_department_number) {

                    $result = self::noMaterial($specification->designation_entry_id, $specification->designationMaterial->isNotEmpty(), $type, $sender_department_number);
                    if ($result == 0 || (is_array($result) && $result['status'] == 0)) {
                        return $type == 0 ? 0 : ['status' => 0, 'designation_entry_id' => $specification->designation_entry_id];
                    }
                    /*if ($result == 0) {
                        return 0;
                    }*/
                }
            }
        }else{

            if (!$material) {
                return $type == 0 ? 0 : ['status' => 0, 'designation_entry_id' => $designation_id];
                //return 0;
            }
        }
        return $type == 0 ? 1 : ['status' => 1, 'designation_entry_id' => ''];
        //return 1;
    }
}
