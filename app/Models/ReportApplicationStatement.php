<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'order_name_id',
        'order_id',
        'tm',
        'tm1',
        'hcp',
        'order_designation',
        'order_designationEntry',
        'order_designationEntry_letters',
        'order_designation_letters'
    ];

    public function designationMaterial(): HasMany
    {
        return $this->hasMany(DesignationMaterial::class, 'designation_id','designation_entry_id');
    }

    /**
     * Определение отношения belongsTo к модели Designation (по полю designation_entry_id).
     *
     * @return BelongsTo
     */
    public function designationEntry(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'designation_entry_id');
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }
    // Включите временные метки
    public $timestamps = true;
}
