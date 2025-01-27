<?php

namespace App\Services\HelpService;

use App\Models\Specification;
use Illuminate\Support\Facades\Log;

class NoMaterialService
{
    public static function noMaterial($designation_id,$material,$type=0): int|array
    {
        $specifications = Specification
            ::where('designation_id', $designation_id)
            ->with(['designations', 'designationEntry', 'designationMaterial'])
            ->get();

        if ($specifications->isNotEmpty()) {
            foreach ($specifications as $specification) {

                if (str_starts_with($specification->designationEntry->designation, 'КР') || str_starts_with($specification->designationEntry->designation, 'ПИ0')) {
                    continue;
                }

                $result = self::noMaterial($specification->designation_entry_id, $specification->designationMaterial->isNotEmpty());
               // Log::info('material111');
                //Log::info(print_r($result,1));
                if ($result == 0 || (is_array($result) && $result['status'] == 0)) {
                    return $type == 0 ? 0 : ['status' => 0, 'designation_entry_id' => $specification->designation_entry_id];
                }
            }
        }else{

            if (!$material) {
                return $type==0 ? 0 : ['status' => 0, 'designation_entry_id' => $designation_id];
            }
        }
        return $type==0 ? 1 : ['status' => 1, 'designation_entry_id' => null];
    }

}
