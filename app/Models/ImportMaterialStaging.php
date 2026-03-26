<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportMaterialStaging extends Model
{
    use HasFactory;

    protected $fillable = [
        'article',
        'name',
        'quantity',
        'document_number',
        'document_date',
        'department_id',
        'type_unit_id',
        'status',
        'resolved_material_id',
    ];

    public function unit()
    {
        return $this->belongsTo(TypeUnit::class, 'type_unit_id');
    }
}
