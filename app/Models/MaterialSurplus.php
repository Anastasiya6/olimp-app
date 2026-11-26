<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialSurplus extends Model
{
    protected $fillable = ['import_material_id', 'quantity', 'department_id', 'status'];

    protected $attributes = ['status' => 'draft'];

    protected $casts = ['quantity' => 'decimal:3'];

    protected static function booted(): void
    {
        static::creating(function (self $document) {
            if (auth()->check()) {
                $document->created_by = auth()->id();
                $document->updated_by = auth()->id();
            }
        });

        static::updating(function (self $document) {
            if ($document->isDirty('created_by')) {
                $document->created_by = $document->getOriginal('created_by');
            }

            if (auth()->check()) {
                $document->updated_by = auth()->id();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(ImportMaterial::class, 'import_material_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
