<?php

namespace Controller;

use Model\Employee;
use Model\Role;
use Model\User;
use Src\Request;
use Src\Validator\Validator;
use Src\View;

class EmployeeController
{
    public function employees_list(): string
    {
        $employees = \Model\Employee::with(['user.department', 'subjects'])->get();
        return new View('site.employees_list', ['employees' => $employees]);
    }

    public function createEmployee(Request $request): string
    {
        if (!app()->auth->user()->isAdmin()) {
            app()->route->redirect('/hello');
        }

        $roles = Role::whereIn('id', [2, 3])->get();
        $departaments = \Model\Departament::all();
        $subjects = \Model\Subject::all();

        if ($request->method === 'POST') {
            $validator = new Validator($request->all(), [
                'login' => ['required', 'unique:users,login'],
                'password' => ['required', 'min:6'],
                'role_id' => ['required', 'exists:roles,id'],
                'last_name' => ['required', 'regex:/^[А-ЯЁа-яё -]+$/u'],
                'first_name' => ['required', 'regex:/^[А-ЯЁа-яё -]+$/u'],
                'middle_name' => ['nullable', 'regex:/^[А-ЯЁа-яё -]+$/u'],
                'gender' => ['required', 'in:1,2'],
                'birth_date' => ['required', 'date'],
                'address' => ['required'],
                'position' => ['required'],
                'department_id' => ['required', 'exists:departaments,id'],
                'subject_id' => ['nullable', 'exists:subjects,id'],
                'hours' => ['required_with:subject_id', 'date_format:H:i']
            ], [
                'required' => 'Поле :field обязательно для заполнения',
                'unique' => 'Логин уже занят',
                'exists' => 'Некорректное значение',
                'min' => 'Минимум :min символов',
                'regex' => 'Допустимы только русские буквы и дефисы',
                'date' => 'Некорректная дата',
                'in' => 'Некорректный пол',
                'date_format' => 'Формат времени: ЧЧ:ММ',
                'required_with' => 'Укажите часы для дисциплины'
            ]);

            if ($validator->fails()) {
                return new View('site.create', [
                    'errors' => $validator->errors(),
                    'old' => $request->all(),
                    'roles' => $roles,
                    'departaments' => $departaments,
                    'subjects' => $subjects
                ]);
            }

            try {
                $data = $request->all();

                $user = User::create([
                    'login' => $data['login'],
                    'password' => $data['password'],
                    'role_id' => $data['role_id']
                ]);

                $employee = Employee::create([
                    'user_id' => $user->id,
                    'last_name' => $data['last_name'],
                    'first_name' => $data['first_name'],
                    'middle_name' => $data['middle_name'] ?? null,
                    'gender' => $data['gender'],
                    'birth_date' => $data['birth_date'],
                    'address' => $data['address'],
                    'post' => $data['position']
                ]);

                if ($departament = \Model\Departament::find($data['department_id'])) {
                    $departament->update(['user_id' => $user->id]);
                }

                if (!empty($data['subject_id'])) {
                    $employee->subjects()->attach($data['subject_id'], [
                        'hours' => $data['hours'] ?? '00:00'
                    ]);
                }

                app()->route->redirect('/employees_list');
            } catch (\Exception $e) {
                return new View('site.create', [
                    'errors' => ['database' => ['Ошибка сохранения данных']],
                    'old' => $request->all(),
                    'roles' => $roles,
                    'departaments' => $departaments,
                    'subjects' => $subjects
                ]);
            }
        }

        return new View('site.create', [
            'roles' => $roles,
            'departaments' => $departaments,
            'subjects' => $subjects
        ]);
    }

    public function employeeSearch(Request $request): string
    {
        if (app()->auth->user()->role_id != 2) {
            app()->route->redirect('/hello');
        }

        $employees = \Model\Employee::orderBy('last_name')->get();
        $results = null;

        // Валидация и санитизация входных данных
        $employeeId = (int)$request->get('employees_id', 0);

        if ($request->method === 'GET' && $employeeId > 0) {
            // Используем безопасный запрос через Eloquent ORM
            $employee = \Model\Employee::with(['subjects' => function($query) {
                $query->select('name')->withPivot('hours');
            }])->where('id', $employeeId)->first();

            if ($employee) {
                $results = [
                    'employee' => $employee,
                    'subjects' => $employee->subjects
                ];
            }
        }

        return new View('site.employee_search', [
            'employees' => $employees,
            'results' => $results,
            'request' => $request
        ]);
    }
    public function editEmployee(int $id, Request $request): string
    {
        $currentUser = app()->auth->user();

        if (!$currentUser->isDeaneryEmployee()) {
            app()->route->redirect('/hello');
        }

        $employee = \Model\Employee::with(['subjects', 'user'])->findOrFail($id);
        $subjects = \Model\Subject::all();

        if ($request->method === 'POST') {
            $validator = new Validator($request->all(), [
                'post' => ['required', 'min:3'],
                'subject_id' => ['nullable', 'exists:subjects,id'],
                'hours' => ['required_with:subject_id', 'date_format:H:i'],
                'avatar' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048']
            ], [
                'required' => 'Поле :field обязательно для заполнения',
                'min' => 'Минимум :min символа',
                'exists' => 'Некорректная дисциплина',
                'date_format' => 'Формат времени: ЧЧ:ММ',
                'required_with' => 'Укажите часы для дисциплины',
                'file' => 'Поле :field должно быть файлом',
                'mimes' => 'Допустимые форматы: jpg, jpeg, png',
                'max' => 'Максимальный размер файла: 2 МБ'
            ]);

            if ($validator->fails()) {
                return new View('site.edit_employee', [
                    'employee' => $employee,
                    'subjects' => $subjects,
                    'hours' => $request->get('hours', '00:00'),
                    'errors' => $validator->errors(),
                    'old' => $request->all()
                ]);
            }

            try {
                $data = $request->all();

                // Обработка загрузки аватара
                if (!empty($_FILES['avatar']['tmp_name'])) {
                    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/public/storage/avatars/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $extension = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                    $filename = uniqid() . '.' . $extension;
                    $destination = $uploadDir . $filename;

                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destination)) {
                        $employee->user->avatar = 'storage/avatars/' . $filename;
                        $employee->user->save();
                    }
                }

                // Обновление должности
                $employee->update(['post' => $data['post']]);

                // Обновление дисциплин и часов
                if (!empty($data['subject_id'])) {
                    $employee->subjects()->sync([
                        $data['subject_id'] => ['hours' => $data['hours'] ?? '00:00']
                    ]);
                } else {
                    $employee->subjects()->detach();
                }

                app()->route->redirect('/employees_list?success=1');
            } catch (\Exception $e) {
                return new View('site.edit_employee', [
                    'employee' => $employee,
                    'subjects' => $subjects,
                    'hours' => $request->get('hours', '00:00'),
                    'errors' => ['database' => ['Ошибка сохранения: ' . $e->getMessage()]],
                    'old' => $request->all()
                ]);
            }
        }

        $hours = $employee->subjects->first()->pivot->hours ?? '00:00';
        $formattedHours = substr($hours, 0, 5);

        return new View('site.edit_employee', [
            'employee' => $employee,
            'subjects' => $subjects,
            'hours' => $formattedHours,
            'errors' => [],
            'old' => $request->all()
        ]);
    }
}