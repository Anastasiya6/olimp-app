<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    CONST DEFAULT_DEPARTMENT = 25;

    CONST DEFAULT_FIRST_DEPARTMENT_ID = 2;

    CONST DEFAULT_SECOND_DEPARTMENT_ID = 3;

    CONST FIRST_DEPARTMENT = '06';

    CONST DEPARTMENT_25_ID = 5;

    public $timestamps = true;
}
