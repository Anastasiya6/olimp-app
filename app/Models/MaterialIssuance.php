<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterialIssuance extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_name_id',
        'designation_id',
        'quantity'
    ];

    public function designation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function designationMaterial(): HasMany
    {
        return $this->hasMany(DesignationMaterial::class, 'designation_id','designation_id');
    }
}
