<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'quantity_total',
        'category_code',
        'designation_entry_id',
        'order_number',
        'order_designationEntry',
        'order_designationEntry_letters',
        'is_report_application_statement',
        'tm'
    ];

    /**
     * Определение отношения belongsTo к модели Designation (по полю designation_id).
     *
     * @return BelongsTo
     */
    public function designations(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    /**
     * Определение отношения belongsTo к модели Designation (по полю designation_entry_id).
     *
     * @return BelongsTo
     */
    public function designationEntry(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'designation_entry_id')->orderBy('designation');
    }
}
