<?php

namespace Middlewares;

use Src\Auth\Auth;
use Src\Request;
use Src\View;

class AuthMiddleware
{
    public function handle(Request $request): Request {
        $token = $request->headers['Authorization'];

        if (empty($token) || !Auth::byToken($token)) {
            (new View())->toJSON(['error' => 'Unauthorized'], 401);
            exit();
        }

        return $request;
    }
}
