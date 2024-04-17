<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_id',
        'material_entry_id',
        'norm'
    ];

    public function materialEntry()
    {
        return $this->belongsTo(Material::class, 'material_entry_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
