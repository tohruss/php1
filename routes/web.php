<?php

use Src\Route;

Route::add('GET', '/hello', [Controller\Site::class, 'hello'])->middleware('auth');
Route::add(['GET', 'POST'], '/signup', [Controller\Site::class, 'signup']);
Route::add(['GET', 'POST'], '/login', [Controller\Site::class, 'login']);
Route::add('GET', '/logout', [Controller\Site::class, 'logout']);
Route::add('GET', '/office', [Controller\Site::class, 'office']);
Route::add('GET', '/create', [Controller\Site::class, 'create']);
Route::add('GET', '/employees_list', [Controller\Site::class, 'employees_list'])->middleware('auth');
Route::add('GET', '/departaments_list', [Controller\Site::class, 'departaments_list'])->middleware('auth');