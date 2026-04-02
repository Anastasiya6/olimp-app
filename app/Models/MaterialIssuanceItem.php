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

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function importMaterial()
    {
        return $this->belongsTo(ImportMaterial::class, 'import_material_id');
    }
}
