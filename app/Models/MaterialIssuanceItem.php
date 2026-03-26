<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialIssuanceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_issuance_id',
        'material_id',
        'import_material_id',
        'quantity'
    ];
}
