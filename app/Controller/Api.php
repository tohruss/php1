<?php

namespace Controller;

use Model\Post;
use Model\User;
use Src\Auth\Auth;
use Src\Request;
use Src\View;

class Api
{
    public function index(): void
    {
        $posts = Post::all()->toArray();

        (new View())->toJSON($posts);
    }

    public function echo(Request $request): void
    {
        (new View())->toJSON($request->all());
    }

    public function login(Request $request): void {
        $credentials = $request->all();

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = bin2hex(random_bytes(32));
            $user->update(['api_token' => $token]);

            (new View())->toJSON([
                'token' => $token
            ]);
        } else {
            (new View())->toJSON(['error' => 'Invalid credentials'], 401);
        }
    }
    public function office(Request $request): void
    {
        // Получаем токен из заголовка Authorization
        $authHeader = $request->headers['Authorization'] ?? '';
        $token = str_replace('Bearer ', '', $authHeader);

        if (empty($token)) {
            (new View())->toJSON(['error' => 'Token required'], 401);
            return;
        }

        // Проверяем токен
        $user = User::where('api_token', $token)->first();

        if (!$user) {
            (new View())->toJSON(['error' => 'Invalid token'], 401);
            return;
        }

        // Если токен верный - возвращаем данные для office
        (new View())->toJSON([
            'user' => [
                'id' => $user->id,
                'login' => $user->login,
                'role' => $user->role->name
            ],
            'employee_data' => $user->employee ? [
                'full_name' => $user->employee->getFullName(),
                'position' => $user->employee->position
            ] : null
        ]);
    }

}
