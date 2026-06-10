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

    public static function checkSameRoute($route){

        $routeParts = explode('-', $route);

        $firstRoute = $routeParts[0] ?? null;
        $secondRoute = $routeParts[1] ?? null;

        if ($firstRoute === $secondRoute) {
            return true;
        }
        return false;
    }

    public static function getLastRoute($route)
    {
        return substr($route, -2);
    }
}
