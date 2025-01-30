<?php

namespace App\Services\HelpService;

class SpecificationService
{
    public static function getRoute($specification,$tm,$department_number)
    {
        if (str_starts_with($specification->designationEntry->designation, 'КР') || $specification->designationEntry->type == 1) {
            // Строка начинается с 'КР' или это ПИ0

            return $department_number == 0 ? 0 : substr($tm, -2);

        }else {

            return $department_number == 0 ? 0 : substr($specification->designationEntry->route, 0, 2);
        }
    }
}
