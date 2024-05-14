<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignationMaterialLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation_id',
        'material_id',
        'designation',
        'detail',
        'designation_number',
        'material',
        'message'
    ];
}
