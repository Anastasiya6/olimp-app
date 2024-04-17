<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation',
        'name',
        'route',
        'gost',
        'type_unit',
        'type',
        'designation_number',
        'designation_from_rascex',
        'designation_from_excel',
        'type_unit_id'
    ];

    public function unit()
    {
        return $this->belongsTo(TypeUnit::class, 'type_unit_id');
    }

    // Включите временные метки
    public $timestamps = true;
}
