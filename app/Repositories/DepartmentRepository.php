<?php

namespace App\Repositories;

use App\Models\Department;
use App\Repositories\Interfaces\DepartmentRepositoryInterface;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    public function getByDepartmentIdFirst($id)
    {
        return Department
            ::where('id',$id)
            ->first();
    }
}
