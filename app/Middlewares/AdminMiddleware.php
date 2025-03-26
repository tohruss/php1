<?php

namespace Middlewares;

use Src\Auth\Auth;
use Src\Request;

class AdminMiddleware
{
    public function handle(Request $request, $params = null)
    {
        // Проверяем, что пользователь авторизован и является администратором
        if (!Auth::check() || Auth::user()->role_id !== 1) {
            app()->route->redirect('/hello');
        }
    }
}