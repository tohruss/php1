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

}