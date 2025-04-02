<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation_id',
        'designation_entry_id',
        'material_id',
        'norm',
        'code_1c'
    ];

    /**
     * Определение отношения belongsTo к модели Designation (по полю designation_id).
     *
     * @return BelongsTo
     */
    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    /**
     * Определение отношения belongsTo к модели Designation (по полю designation_entry_id).
     *
     * @return BelongsTo
     */
    public function designationEntry(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'designation_entry_id')->orderBy('designation');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
