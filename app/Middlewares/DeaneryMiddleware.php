<?php

namespace Middlewares;

use Src\Auth\Auth;
use Src\Request;

class DeaneryMiddleware
{
    public function handle(Request $request, $params = null)
    {
        // Проверяем, что пользователь авторизован и является сотрудником деканата
        if (!Auth::check() || Auth::user()->role_id !== 2) {
            app()->route->redirect('/hello');
        }
    }
}