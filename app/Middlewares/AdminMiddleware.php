<?php

namespace Middlewares;

use Src\Auth\Auth;
use Src\Request;

class AdminMiddleware
{
    public function handle(Request $request, $params = null)
    {
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            app()->route->redirect('/hello');
        }
    }
}