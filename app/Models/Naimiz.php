<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Naimiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'ediz',
        'naimiz'
    ];
}
