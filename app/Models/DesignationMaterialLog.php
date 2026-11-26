<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesignationMaterialLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'designation_id',
        'material_id',
        'designation',
        'detail',
        'designation_number',
        'material',
        'message',
        'norm'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
