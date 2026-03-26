<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportMaterialStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_material_id',
        'amount',
        'type',
        'document_number',
        'document_date',
        'department_id'];

    public function materials()
    {
        return $this->belongsTo(ImportMaterial::class, 'import_material_id');
    }

    public function unit()
    {
        return $this->belongsTo(TypeUnit::class, 'type_unit_id');
    }

}
