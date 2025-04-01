<?php

namespace Controller;

use Model\Post;
use Src\Validator\Validator;
use Src\View;
use Src\Request;
use Model\User;
use Src\Auth\Auth;
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
            // Валидация данных
            $validator = new Validator($request->all(), [
                'login' => ['required', 'unique:users,login'],
                'password' => ['required']
            ], [
                'required' => 'Поле :field обязательно для заполнения',
                'unique' => 'Пользователь с таким :field уже существует'
            ]);

            if ($validator->fails()) {
                return new View('site.signup', [
                    'errors' => $validator->errors(),
                    'old' => $request->all()
                ]);
            }

            try {
                // Подготовка данных для регистрации
                $userData = $request->all();
                $userData['role_id'] = isset($userData['is_admin']) && $userData['is_admin'] == '1' ? 1 : 3;
                unset($userData['is_admin']);

                // Создание пользователя
                if (User::create($userData)) {
                    app()->route->redirect('/hello');
                }
            } catch (\PDOException $e) {
                // Обработка ошибки дубликата логина
                if (strpos($e->getMessage(), 'Integrity constraint violation') !== false) {
                    return new View('site.signup', [
                        'errors' => ['login' => ['Пользователь с таким логином уже существует']],
                        'old' => $request->all()
                    ]);
                }
                // Обработка других ошибок
                return new View('site.signup', [
                    'errors' => ['database' => ['Ошибка регистрации: ' . $e->getMessage()]],
                    'old' => $request->all()
                ]);
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

}