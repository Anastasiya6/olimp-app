<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialReturn extends Model
{
    protected $fillable = ['import_material_id', 'quantity', 'department_id', 'status'];

    protected $attributes = ['status' => 'draft'];

    protected $casts = ['quantity' => 'decimal:3'];

    public function material(): BelongsTo
    {
        return $this->belongsTo(ImportMaterial::class, 'import_material_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
