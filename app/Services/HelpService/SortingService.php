<?php

namespace App\Services\HelpService;

class SortingService
{
    public function getSortByDepartment($data)
    {
        return $data->sort(function ($a, $b) {
            // Если у первого элемента нет цеха, а у второго есть - второй элемент должен быть выше
            if (empty($a['department']) && !empty($b['department'])) {
                return 1;
            }

            // Если у второго элемента нет цеха, а у первого есть - первый элемент должен быть выше
            if (!empty($a['department']) && empty($b['department'])) {
                return -1;
            }

            // Если у обоих элементов есть цеха или у обоих нет - сортируем по названию цеха
            return strcmp($a['department'], $b['department']);
        });
    }
}
