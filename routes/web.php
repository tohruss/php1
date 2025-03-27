<?php

use Src\Route;

Route::add('GET', '/hello', [Controller\Site::class, 'hello']);
Route::add(['GET', 'POST'], '/signup', [Controller\Site::class, 'signup']);
Route::add(['GET', 'POST'], '/login', [Controller\Site::class, 'login']);
Route::add('GET', '/logout', [Controller\Site::class, 'logout']);
Route::add('GET', '/office', [Controller\Site::class, 'office'])->middleware('auth');
Route::add(['GET', 'POST'], '/create', [Controller\Site::class, 'createEmployee'])->middleware('auth');
Route::add('GET', '/employees_list', [Controller\Site::class, 'employees_list'])->middleware('auth');
Route::add('GET', '/departaments_list', [Controller\Site::class, 'departaments_list'])->middleware('auth');
Route::add('GET', '/subjects_list', [Controller\Site::class, 'subjects_list'])->middleware('auth');