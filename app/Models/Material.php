<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type_unit_id',
        'code_1c'
    ];

    protected static function booted(): void
    {
        static::creating(function (self $material) {
            $material->created_by = auth()->id();
            $material->updated_by = auth()->id();
        });

        static::updating(function (self $material) {
            if (auth()->check() && $material->isDirty(['name', 'type_unit_id', 'code_1c'])) {
                $material->updated_by = auth()->id();
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

    public function unit()
    {
        return $this->belongsTo(TypeUnit::class, 'type_unit_id');
    }
    public function material()
    {
        return $this->hasMany(GroupMaterial::class, 'material_id');
    }
    public function materialEntry()
    {
        return $this->hasMany(GroupMaterial::class, 'material_entry_id','id');
    }
    public function groupMaterials()
    {
        return $this->hasMany(GroupMaterial::class, 'material_id');
    }
    public function groupMaterialsEntry()
    {
        return $this->hasMany(GroupMaterial::class, 'material_entry_id');
    }
    public function parentMaterials()
    {
        return $this->belongsToMany(Material::class, 'group_materials', 'material_entry_id', 'material_id');
    }
}
