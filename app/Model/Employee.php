<?php

namespace Model;

use Illuminate\Database\Eloquent\Model;
use Collect\Validation;

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
        'post',
        'avatar'
    ];

    public static $createValidationRules = [
        // Поля User
        'login' => ['required', 'unique:users,login'],
        'password' => ['required', Validation::PASSWORD],
        'role_id' => ['required', 'exists:roles,id'],

        'last_name' => ['required', Validation::NAME_PATTERN],
        'first_name' => ['required', Validation::NAME_PATTERN],
        'middle_name' => ['nullable', Validation::NAME_PATTERN],
        'gender' => ['required', 'in:1,2'],
        'birth_date' => ['required', 'date'],
        'address' => ['required', Validation::NAME_PATTERN],
        'position' => ['required', Validation::NAME_PATTERN],
        'department_id' => ['required', 'exists:departaments,id'],
        'subject_id' => ['nullable', 'exists:subjects,id'],
        'hours' => ['required_with:subject_id', Validation::HOURS_PATTERN]
    ];

    public static $createValidationMessages = [
        'required' => 'Поле :field обязательно для заполнения',
        'unique' => 'Логин уже занят',
        'exists' => 'Некорректное значение',
        'regex' => 'Допустимы только русские буквы и дефисы',
        'date' => 'Некорректная дата',
        'in' => 'Некорректный пол',
        'date_format' => 'Формат времени: ЧЧ:ММ',
        'required_with' => 'Укажите часы для дисциплины'
    ];

    public static $editValidationRules = [
        'post' => ['required', Validation::NAME_PATTERN],
        'subject_id' => ['nullable', 'exists:subjects,id'],
        'hours' => ['required_with:subject_id', Validation::HOURS_PATTERN],
        'avatar' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048']
    ];

    public static $editValidationMessages = [
        'required' => 'Поле :field обязательно для заполнения',
        'exists' => 'Некорректная дисциплина',
        'date_format' => 'Формат времени: ЧЧ:ММ',
        'required_with' => 'Укажите часы для дисциплины',
        'file' => 'Поле :field должно быть файлом',
        'mimes' => 'Допустимые форматы: jpg, jpeg, png',
        'max' => 'Максимальный размер файла: 2 МБ'
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
            'employees_id',
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