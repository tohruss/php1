<?php

namespace Model;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'subjects';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'create_at'
    ];

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_subj', 'subjects_id', 'employees_id')
            ->withPivot('hours');
    }
}