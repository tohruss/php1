<?php
return [
    //Класс аутентификации
    'auth' => \Src\Auth\Auth::class,
    //Клас пользователя
    'identity' => \Model\User::class,
    //Классы для middleware
    'routeMiddleware' => [
        'auth' => \Middlewares\AuthMiddleware::class,
        'admin' => \Middlewares\AdminMiddleware::class,
        'deanery' => \Middlewares\DeaneryMiddleware::class,
        'admin_or_deanery' => \Middlewares\AdminOrDeaneryMiddleware::class,
    ],
    'validators' => [
        'required' => \Validators\RequireValidator::class,
        'unique' => \Validators\UniqueValidator::class,
    ],
    'routeAppMiddleware' => [
        'trim' => \Middlewares\TrimMiddleware::class,
        'specialChars' => \Middlewares\SpecialCharsMiddleware::class,
        'csrf' => \Middlewares\CSRFMiddleware::class,
        'json' => \Middlewares\JSONMiddleware::class,
    ],
    'providers' => [
        'kernel' => Providers\KernelProvider::class,
        'route' => Providers\RouteProvider::class,
        'db' => Providers\DBProvider::class,
        'auth' => Providers\AuthProvider::class,
    ],




];
