<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignationMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation_id',
        'material_id',
        'norm',
        'designation_from_excel',
        'material_from_excel',
        'department_id'
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function reportApplicationStatements()
    {
        return $this->hasMany(ReportApplicationStatement::class, 'designation_entry_id', 'designation_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
