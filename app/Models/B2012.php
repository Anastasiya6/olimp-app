<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class B2012 extends Model
{
    use HasFactory;

    protected $fillable = [
        'kuda',
        'zakaz',
        'chto',
        'kols',
        'kolzak',
        'tm',
        'tm1',
        'naim',
        'hcp',
        'e',
    ];
}
