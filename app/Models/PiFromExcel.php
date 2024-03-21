<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PiFromExcel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type_unit_id',
    ];
}
