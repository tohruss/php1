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
        if ($request->method === 'GET') {
            return new View('site.login');
        }

        $validator = new Validator($request->all(), [
            'login' => ['required'],
            'password' => ['required'],
        ], [
            'required' => 'Поле :field пусто',
        ]);

        if ($validator->fails()) {
            return new View('site.login', [
                'message' => json_encode($validator->errors(), JSON_UNESCAPED_UNICODE)
            ]);
        }

        if (Auth::attempt($request->all())) {
            app()->route->redirect('/office');
            return '';
        }

        return new View('site.login', ['message' => 'Вы ввели некорректные данные']);
    }

    public function logout(): void
    {
        Auth::logout();
        app()->route->redirect('/hello');
    }

    public function signup(Request $request): string
    {
        if ($request->method === 'POST') {
            $validator = new Validator($request->all(), [
                'login' => ['required', 'unique:users,login'],
                'password' => ['required']
            ], [
                'required' => 'Поле :field пусто',
                'unique' => 'Поле :field должно быть уникально'
            ]);

            if ($validator->fails()) {
                return new View('site.signup', [
                    'message' => json_encode($validator->errors(), JSON_UNESCAPED_UNICODE)
                ]);
            }

            $userData = $request->all();
            $userData['role_id'] = 1;

            if (User::create($userData)) {
                app()->route->redirect('/login');
                return '';
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