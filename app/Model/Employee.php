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
        return $this->belongsTo(User::class, 'user_id');
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

    public function department()
    {
        return $this->hasOneThrough(
            Departament::class,
            User::class,
            'id', // Поле в users
            'user_id', // Поле в departaments
            'user_id', // Поле в employees
            'id' // Поле в users
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