<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class M6pi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nm',
        'naim',
        'gost',
        'ediz',
    ];
}
