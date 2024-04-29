<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation',
        'name',
        'route',
        'gost',
        'type_unit',
        'type',
        'designation_number',
        'designation_from_rascex',
        'designation_from_excel',
        'type_unit_id'
    ];
    // Включите временные метки
    public $timestamps = true;

    public function unit()
    {
        return $this->belongsTo(TypeUnit::class, 'type_unit_id');
    }

    // Дети текущего элемента (исходящие связи)
    /*public function childrenSpecifications()
    {
        return $this->hasMany(Specification::class, 'designation_id');
    }*/

    // Дети текущего элемента через модель Designation
    public function children()
    {
        return $this->hasManyThrough(
            Designation::class,
            Specification::class,
            'designation_id', // Foreign key on specifications table...
            'id', // Foreign key on designations table...
            'id', // Local key on designations table...
            'designation_entry_id', // Local key on specifications table...
        )->orderBy('designation');
    }
    public function designationMaterial()
    {
        return $this->hasMany(DesignationMaterial::class, 'designation_id','id');
    }


    // Родители текущего элемента (входящие связи)
   /* public function parentSpecifications()
    {
        return $this->hasMany(Specification::class, 'designation_entry_id');
    }*/

    // Родители текущего элемента через модель Designation
    public function parents()
    {
        return $this->hasManyThrough(
            Designation::class,
            Specification::class,
            'designation_entry_id', // Foreign key on specifications table...
            'id', // Foreign key on designations table...
            'id', // Local key on designations table...
            'designation_id' // Local key on specifications table...
        )->orderBy('designation');;
    }


}
