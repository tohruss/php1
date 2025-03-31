<?php

namespace Middlewares;

use Src\Auth\Auth;
use Src\Request;

class AdminOrDeaneryMiddleware
{
    public function handle(Request $request, $params = null)
    {
        $user = Auth::user();
        if (!Auth::check() || !($user->isAdmin() || $user->isDeaneryEmployee())) {
            app()->route->redirect('/hello');
        }
    }
}