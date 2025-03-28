<?php

namespace Controller;

use Model\Employee;
use Model\Role;
use Model\User;
use Src\Request;
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

        $roles = Role::whereIn('id', [2, 3])->get(); // Роли для сотрудников
        $departaments = \Model\Departament::all();
        $subjects = \Model\Subject::all();

        if ($request->method === 'POST') {
            try {
                $data = $request->all();

                // Валидация
                $required = ['login', 'password', 'role_id', 'last_name',
                    'first_name', 'gender', 'birth_date',
                    'address', 'position', 'department_id'];

                foreach ($required as $field) {
                    if (empty($data[$field])) {
                        throw new \Exception("Поле '$field' обязательно для заполнения");
                    }
                }

                // Создаем пользователя
                $user = User::create([
                    'login' => $data['login'],
                    'password' => $data['password'],
                    'role_id' => $data['role_id']
                ]);

                // Создаем сотрудника
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

                // Привязываем к кафедре
                $departament = \Model\Departament::find($data['department_id']);
                if ($departament) {
                    $user->update(['id' => $user->id]); // Если user_id в departaments ссылается на users.id
                    $departament->update(['user_id' => $user->id]);
                }

                // Привязываем дисциплину (теперь только одну)
                if (!empty($data['subject_id'])) {
                    $employee->subjects()->attach($data['subject_id'], [
                        'hours' => $data['hours'] ?? '00:00'
                    ]);
                }

                app()->route->redirect('/employees_list');
            } catch (\Exception $e) {
                return new View('site.create', [
                    'message' => 'Ошибка: ' . $e->getMessage(),
                    'roles' => $roles,
                    'departaments' => $departaments,
                    'subjects' => $subjects,
                    'old' => $request->all()
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

        // Используем безопасное получение параметра
        $employeeId = $request->get('employees_id', null);

        if ($request->method === 'GET' && $employeeId) {
            $employee = \Model\Employee::with(['subjects' => function($query) {
                $query->select('name')->withPivot('hours');
            }])->find($employeeId);

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

        $employee = \Model\Employee::with(['subjects'])->findOrFail($id);
        $subjects = \Model\Subject::all();

        if ($request->method === 'POST') {
            $data = $request->all();
            $errors = [];

            // Валидация
            if (empty($data['post'])) {
                $errors[] = 'Должность обязательна для заполнения';
            }

            if (empty($errors)) {
                try {
                    // Обновление должности
                    $employee->update(['post' => $data['post']]);

                    // Обновление дисциплин и часов
                    if (!empty($data['subject_id'])) {
                        $hours = $data['hours'] . ':00'; // Форматируем время
                        $employee->subjects()->sync([
                            $data['subject_id'] => ['hours' => $hours]
                        ]);
                    }

                    app()->route->redirect('/employees_list?success=1');
                } catch (\Exception $e) {
                    $errors[] = 'Ошибка: ' . $e->getMessage();
                }
            }
        }

        // Получаем текущие часы
        $hours = $employee->subjects->first()->pivot->hours ?? '00:00';
        $formattedHours = substr($hours, 0, 5); // Обрезаем секунды

        return new View('site.edit_employee', [
            'employee' => $employee,
            'subjects' => $subjects,
            'hours' => $formattedHours,
            'errors' => $errors ?? [],
            'old' => $request->all()
        ]);
    }
}