<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportApplicationStatement extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation_id',
        'designation_entry_id',
        'quantity',
        'category_code',
        'quantity',
        'quantity_total',
        'order_number',
       // 'tm',
        //'tm1',
        //'hcp'
    ];

    // Включите временные метки
    public $timestamps = true;
}
