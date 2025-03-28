<?php

namespace Controller;

use Model\Post;
use Src\View;
use Src\Request;
use Model\User;
use Src\Auth\Auth;
use Model\Role;
use Model\Employee;

class Site
{
    public function login(Request $request): string
    {
        //Если просто обращение к странице, то отобразить форму
        if ($request->method === 'GET') {
            return new View('site.login');
        }
        //Если удалось аутентифицировать пользователя, то редирект
        if (Auth::attempt($request->all())) {
            app()->route->redirect('/hello');
        }
        //Если аутентификация не удалась, то сообщение об ошибке
        return new View('site.login', ['message' => 'Неправильные логин или пароль']);
    }

    public function logout(): void
    {
        Auth::logout();
        app()->route->redirect('/hello');
    }

    public function signup(Request $request): string
    {
        if ($request->method === 'POST') {
            // Подготовка данных для регистрации
            $userData = $request->all();

            // Устанавливаем роль: 1 - админ, если чекбокс отмечен, иначе 3 - обычный пользователь
            $userData['role_id'] = isset($userData['is_admin']) && $userData['is_admin'] == '1' ? 1 : 3;

            // Удаляем поле is_admin, так как в модели его нет
            unset($userData['is_admin']);

            if (User::create($userData)) {
                app()->route->redirect('/hello');
            }
        }
        return new View('site.signup');
    }

    public function index(Request $request): string
    {
        $posts = Post::where('id', $request->id)->get();
        return (new View())->render('site.post', ['posts' => $posts]);
    }

    public function hello(): string
    {
        return new View('site.hello', ['message' => 'hello working']);
    }

    public function office(): string
    {
        $user = app()->auth->user();
        $roleName = $user->role->name ?? 'Пользователь';

        // Загружаем информацию о сотруднике, если это пользователь с id 2 или 3
        $employeeData = null;
        if ($user->role_id == 2 || $user->role_id == 3) {
            $user->load('employee');
            $employeeData = $user->employee;
        }

        return new View('site.office', [
            'message' => 'Добро пожаловать в личный кабинет',
            'user' => $user,
            'roleName' => $roleName,
            'employeeData' => $employeeData
        ]);
    }


    public function employees_list(): string
    {
        $employees = \Model\Employee::with(['user.department', 'subjects'])->get();
        return new View('site.employees_list', ['employees' => $employees]);
    }

    public function departaments_list(): string
    {
        // Загружаем кафедры вместе с создателями (только нужные поля)
        $departaments = \Model\Departament::with(['creator' => function ($query) {
            $query->select('id', 'login'); // Выбираем только id и login
        }])->get();

        return new View('site.departaments_list', [
            'departaments' => $departaments
        ]);
    }

    public function subjects_list(): string
    {
        $user = app()->auth->user();

        // Проверка прав доступа (админ или сотрудник деканата)
        if (!$user->isAdmin() && $user->role_id != 2) {
            app()->route->redirect('/hello');
        }

        $subjects = \Model\Subject::all();

        return new View('site.subjects_list', [
            'subjects' => $subjects,
            'message' => 'Список дисциплин'
        ]);
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

    public function departamentSearch(Request $request): string
    {
        // Проверка прав (только для сотрудников деканата)
        if (!app()->auth->user()->isDeaneryEmployee()) { // Более читаемая проверка
            app()->route->redirect('/hello');
        }

        // Получаем выбранную кафедру из запроса
        $selectedDepartmentId = $request->get('departament_id');

        // Получаем список всех кафедр для выпадающего списка
        $departaments = \Model\Departament::orderBy('name')->get();

        // Если кафедра выбрана - ищем её сотрудников
        if ($selectedDepartmentId) {
            // Находим кафедру
            $departament = \Model\Departament::find($selectedDepartmentId);

            // Получаем сотрудников этой кафедры через отношение
            $employees = $departament ? $departament->employees()->with('user')->get() : collect();
        } else {
            $employees = collect();
        }

        return new View('site.departament_search', [
            'departaments' => $departaments,
            'employees' => $employees,
            'selectedDepartament' => $selectedDepartmentId
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
    private function formatTimeForInput(string $time): string
    {
        return substr($time, 0, 5); // Обрезаем секунды
    }

}