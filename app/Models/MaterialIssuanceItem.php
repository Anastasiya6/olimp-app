<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialIssuanceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_issuance_id',
        'material_id',
        'designation_id',
        'import_material_id',
        'quantity',
        'details'
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function importMaterial()
    {
        return $this->belongsTo(ImportMaterial::class, 'import_material_id');
    }
}
