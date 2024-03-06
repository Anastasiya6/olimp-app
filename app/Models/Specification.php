<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specification extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation',
        'detail',
        'quantity',
        'category_code',
        'designation_id',
        'designation_entry_id'
    ];
    /**
     * Определение отношения belongsTo к модели Designation (по полю designation_id).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function designations()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    /**
     * Определение отношения belongsTo к модели Designation (по полю designation_entry_id).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function designationEntry()
    {
        return $this->belongsTo(Designation::class, 'designation_entry_id');
    }

    // Включите временные метки
    public $timestamps = true;
}
