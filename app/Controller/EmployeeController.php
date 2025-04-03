<?php

namespace Controller;

use Model\Employee;
use Model\Role;
use Model\User;
use Src\Request;
use Src\View;
use helpers\RequestHelp;
use helpers\ResponseHelp;
use Illuminate\Database\Capsule\Manager as Capsule;
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

        $roles = Role::whereIn('name', ['Сотрудник деканата', 'Сотрудник'])->get();
        $departaments = \Model\Departament::all();
        $subjects = \Model\Subject::all();

        if ($request->method === 'POST') {
            // Валидация данных
            $errors = RequestHelp::validate($request->all());

            if (!empty($errors)) {
                ResponseHelp::redirectWithErrors('/create', $errors, $request->all());
            }

            try {
                // Начало транзакции
                Capsule::connection()->transaction(function() use ($request) {
                    $data = $request->all();

                    // Создание пользователя с хешированием пароля
                    $user = User::create([
                        'login' => $data['login'],
                        'password' => md5($data['password']),
                        'role_id' => $data['role_id']
                    ]);

                    // Создание сотрудника
                    $employee = Employee::create([
                        'user_id' => $user->id,
                        'last_name' => $data['last_name'],
                        'first_name' => $data['first_name'],
                        'middle_name' => $data['middle_name'],
                        'gender' => $data['gender'],
                        'birth_date' => $data['birth_date'],
                        'address' => $data['address'],
                        'post' => $data['post']
                    ]);

                    // Обновление кафедры
                    if ($departament = \Model\Departament::find($data['department_id'])) {
                        $departament->update(['user_id' => $user->id]);
                    }

                    // Добавление дисциплины
                    if (!empty($data['subject_id'])) {
                        $employee->subjects()->attach($data['subject_id'], [
                            'hours' => $data['hours'] ?? '00:00'
                        ]);
                    }
                });

                app()->route->redirect('/employees_list');

            } catch (\Exception $e) {
                ResponseHelp::redirectWithErrors(
                    '/create',
                    ['database' => ['Ошибка сохранения: ' . $e->getMessage()]],
                    $request->all()
                );
            }
        }

        // Получение ошибок из сессии
        $errors = ResponseHelp::getSessionErrors();
        ResponseHelp::clearSessionData();

        return new View('site.create', [
            'errors' => $errors,
            'old' => $_SESSION['old'] ?? [],
            'roles' => $roles,
            'departaments' => $departaments,
            'subjects' => $subjects
        ]);
    }

    public function employeeSearch(Request $request): string
    {
        $user = app()->auth->user();
        if (!$user->isDeaneryEmployee()) {
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
        // Проверка прав доступа
        $currentUser = app()->auth->user();
        if (!$currentUser->isDeaneryEmployee()) {
            app()->route->redirect('/hello');
        }

        $employee = \Model\Employee::with(['subjects', 'user'])->findOrFail($id);
        $subjects = \Model\Subject::all();

        if ($request->method === 'POST') {
            // Валидация данных с использованием RequestHelp
            $errors = RequestHelp::validateEdit($request->all(), $_FILES);

            if (!empty($errors)) {
                return new View('site.edit_employee', [
                    'employee' => $employee,
                    'subjects' => $subjects,
                    'hours' => $request->get('hours', '00:00'),
                    'errors' => $errors,
                    'old' => $request->all()
                ]);
            }

            try {
                Capsule::connection()->transaction(function() use ($employee, $request) {
                    $data = $request->all();

                    // Обработка аватара
                    if (!empty($_FILES['avatar']['tmp_name'])) {
                        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/public/storage/img/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }

                        // Удаление старого аватара
                        if ($employee->user->avatar && file_exists($_SERVER['DOCUMENT_ROOT'] . '/public/' . $employee->user->avatar)) {
                            unlink($_SERVER['DOCUMENT_ROOT'] . '/public/' . $employee->user->avatar);
                        }

                        // Сохранение нового аватара
                        $extension = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                        $filename = uniqid() . '.' . $extension;
                        $destination = $uploadDir . $filename;

                        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destination)) {
                            $employee->user->avatar = 'storage/img/' . $filename;
                            $employee->user->save();
                        }
                    }

                    // Обновление данных сотрудника
                    $employee->update(['post' => $data['post']]);

                    // Обновление дисциплин и часов
                    if (!empty($data['subject_id'])) {
                        $employee->subjects()->sync([
                            $data['subject_id'] => ['hours' => $data['hours'] ?? '00:00']
                        ]);
                    } else {
                        $employee->subjects()->detach();
                    }
                });

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

        // Подготовка данных для формы
        $hours = $employee->subjects->first()->pivot->hours ?? '00:00';
        $formattedHours = substr($hours, 0, 5);

        return new View('site.edit_employee', [
            'employee' => $employee,
            'subjects' => $subjects,
            'hours' => $formattedHours,
            'errors' => [],
            'old' => [
                'post' => $employee->post,
                'subject_id' => $employee->subjects->first()->id,
                'hours' => $formattedHours
            ]
        ]);
    }
}