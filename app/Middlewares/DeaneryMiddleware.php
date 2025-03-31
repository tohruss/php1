<?php

namespace Middlewares;

use Src\Auth\Auth;
use Src\Request;

class DeaneryMiddleware
{
    public function handle(Request $request, $params = null)
    {
        $user = Auth::user();
        if (!Auth::check() || !$user->isDeaneryEmployee()) {
            app()->route->redirect('/hello');
        }
    }
}