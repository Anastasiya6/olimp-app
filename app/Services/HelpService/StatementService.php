<?php

namespace App\Services\HelpService;

use App\Models\Specification;

class StatementService
{
    public static function getTm(Specification $specification): ?string
    {
        $tm = 0;
        if (isset($specification->designationEntry) && isset($specification->designations)) {
            if ($specification->designationEntry->route == "" && $specification->designations->route != "" && !str_ends_with($specification->designations->route, '99')) {
                $tm = "99";
            } elseif ($specification->designationEntry->id == $specification->designations->id && $specification->designationEntry->route == "" && $specification->designations->route == "") {
                $tm = "99";
            } elseif (substr($specification->designationEntry->route, 0, 2) == substr($specification->designations->route, 0, 2) && $specification->designationEntry->route != "") {
                $tm = $specification->designationEntry->route;
            } elseif (substr($specification->designationEntry->route, -2) == $specification->designations->route) {
                $tm = $specification->designationEntry->route;
            } elseif (substr($specification->designations->route, 0, 2) != "") {
                if(!str_ends_with($specification->designationEntry->route, '99') && substr($specification->designationEntry->route, -2) != substr($specification->designations->route, 0,2)){
                    $tm = $specification->designationEntry->route ? $specification->designationEntry->route."-99":$specification->designationEntry->route."99";
                }else{
                    $tm = $specification->designationEntry->route;
                }
            } elseif (substr($specification->designations->route, 0, 2) == "") {
                $tm = $specification->designationEntry->route;
            }
        }

        if(strpos($specification->designationEntry->route, substr($specification->designations->route,0,2)) !== false && strpos($specification->designationEntry->route, '99') !== false){

            // Удаляем '99' из строки с дефисом, если он есть перед '99'
            $tm = preg_replace('/-\s*99/', '', $tm);

            // Удаляем '99' из строки без дефиса, если он не имеет дефиса перед собой
            $tm = preg_replace('/(?<!-)\s*99/', '', $tm);

            // Удаляем лишние пробелы перед и после '99'
            $tm = trim($tm);

        }

        if($specification->designations->route!=''){
            $tm = $tm.'-'.substr($specification->designations->route, 0, 2);
        }
      //  if($tm == null)
           // $tm = '';
            //dd($specification);
        return $tm;

    }
}
