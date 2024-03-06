<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nacop extends Model
{
    use HasFactory;

    protected $fillable = [
        'od',
        'e',
        'ok',
        'pe',
        'pi',
        'na',
    ];
}
