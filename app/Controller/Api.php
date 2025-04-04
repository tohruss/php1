<?php

namespace Controller;

use Model\Post;
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
                'token' => $token,
                'user_id' => $user->id
            ]);
        } else {
            (new View())->toJSON(['error' => 'Invalid credentials'], 401);
        }
    }

}
