<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class DeliveryNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_number',
        'document_date',
        'designation_id',
        'quantity',
        'order_name_id',
        'sender_department_id',
        'receiver_department_id',
        'material_id',
        'is_written_off',
        'with_purchased',
        'with_material_purchased'

    ];

    public function scopeWithFilters(Builder $query, $startDate, $endDate, $selectedOrder, $selectedDepartmentSender, $selectedDepartmentReceiver)
    {
        return $query
            ->whereRaw('DATE(document_date) >= ?', [$startDate])
            ->whereRaw('DATE(document_date) <= ?', [$endDate])
            //->where('is_written_off',  $is_written_off)
            ->where('order_name_id', $selectedOrder)
            ->where('sender_department_id', $selectedDepartmentSender)
            ->where('receiver_department_id', $selectedDepartmentReceiver);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function orderName()
    {
        return $this->belongsTo(OrderName::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function designationMaterial(): HasMany
    {
        return $this->hasMany(DesignationMaterial::class, 'designation_id','designation_id');
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
