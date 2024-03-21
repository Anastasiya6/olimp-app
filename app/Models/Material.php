<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type_unit_id',
    ];

    public function unit()
    {
        return $this->belongsTo(TypeUnit::class, 'type_unit_id');
    }


}
