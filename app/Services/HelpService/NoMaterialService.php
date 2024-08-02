<?php

namespace App\Services\HelpService;

use App\Models\Specification;

class NoMaterialService
{
    public static function noMaterial($designation_id,$material): int
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
                if ($result == 0) {
                    return 0;
                }
            }
        }else{

            if (!$material) {
                return 0;
            }
        }
        return 1;
    }
}
