<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation_id',
        'designation_entry_id',
        'material_id',
        'designation',
        'detail',
        'designation_number',
        'detail_number',
        'material',
        'message'
    ];
}
