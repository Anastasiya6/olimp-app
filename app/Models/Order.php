<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation_id',
        'quantity',
        'order_name_id'
    ];

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function orderName(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OrderName::class);
    }
}
