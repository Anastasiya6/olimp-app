<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'quantity_total',
        'category_code',
        'designation_id',
        'sender_department_id',
        'receiver_department_id',
        'order_name_id',
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
    public function designation(): BelongsTo
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

    public function designationMaterial(): HasMany
    {
        return $this->hasMany(DesignationMaterial::class, 'designation_id','designation_id');
    }

    public function orderName()
    {
        return $this->belongsTo(OrderName::class);
    }

    public function senderDepartment()
    {
        return $this->belongsTo(Department::class, 'sender_department_id');
    }

    public function receiverDepartment()
    {
        return $this->belongsTo(Department::class, 'receiver_department_id');
    }
}
