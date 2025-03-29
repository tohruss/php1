<?php

namespace Middlewares;

use Src\Auth\Auth;
use Src\Request;

class AdminOrDeaneryMiddleware
{
    public function handle(Request $request, $params = null)
    {
        // Проверяем авторизацию и роль (1 - admin, 2 - deanery)
        if (!Auth::check() || !in_array(Auth::user()->role_id, [1, 2])) {
            app()->route->redirect('/hello');
        }
    }
}