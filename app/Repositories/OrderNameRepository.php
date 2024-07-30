<?php

namespace App\Repositories;

use App\Models\OrderName;
use App\Repositories\Interfaces\OrderNameRepositoryInterface;

class OrderNameRepository implements OrderNameRepositoryInterface
{
    public function getByOrderFirst($id)
    {
        return OrderName
            ::where('id',$id)
            ->first();
    }
}
