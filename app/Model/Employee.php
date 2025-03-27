<?php

namespace Model;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'last_name',
        'first_name',
        'middle_name',
        'gender',
        'birth_date',
        'address',
        'post'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(
            Subject::class,
            'employee_subj',
            'employees_id',  // Это должно соответствовать имени поля в БД
            'subjects_id'
        )->withPivot('hours');
    }

    public function departament()
    {
        return $this->hasOneThrough(
            Departament::class,
            User::class,
            'id',
            'user_id',
            'user_id',
            'id'
        );
    }

    public function getFullName(): string
    {
        return implode(' ', array_filter([
            $this->last_name,
            $this->first_name,
            $this->middle_name
        ]));
    }


}