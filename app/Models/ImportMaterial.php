<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'article',
        'name',
        'type_unit_id',
    ];

    public function stocks()
    {
        return $this->hasMany(ImportMaterialStock::class, 'import_material_id');
    }

    public function unit()
    {
        return $this->belongsTo(TypeUnit::class, 'type_unit_id');
    }
}
