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
        'type_units',
        'type'
    ];

    // Включите временные метки
    public $timestamps = true;
}
