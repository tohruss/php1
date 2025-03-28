<?php

namespace Model;

use Illuminate\Database\Eloquent\Model;

class Departament extends Model
{
    protected $table = 'departaments';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['name', 'user_id', 'create_at'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Обратная связь
    }

    public function employees()
    {
        return $this->hasManyThrough(
            Employee::class,
            User::class,
            'id', // Foreign key on users table
            'user_id', // Foreign key on employees table
            'user_id', // Local key on departments table
            'id' // Local key on users table
        );
    }

    // Новый метод для получения дисциплин кафедры
    public function subjects()
    {
        return $this->hasManyThrough(
            Subject::class,
            Employee::class,
            'user_id', // Foreign key on employees table
            'id', // Foreign key on subjects table (не используется напрямую)
            'user_id', // Local key on departments table
            'id' // Local key on employees table
        )->distinct();
    }

    // Метод для получения сотрудников с дисциплинами
    public function employeesWithSubjects()
    {
        return $this->employees()->with('subjects');
    }
}