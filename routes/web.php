<?php

use Src\Route;

Route::add('GET', '/hello', [Controller\Site::class, 'hello']);
Route::add(['GET', 'POST'], '/signup', [Controller\Site::class, 'signup']);
Route::add(['GET', 'POST'], '/login', [Controller\Site::class, 'login']);
Route::add('GET', '/logout', [Controller\Site::class, 'logout']);
Route::add('GET', '/office', [Controller\UserController::class, 'office'])->middleware('auth');
Route::add(['GET', 'POST'], '/create', [Controller\EmployeeController::class, 'createEmployee'])->middleware('auth', 'admin');
Route::add('GET', '/employees_list', [Controller\EmployeeController::class, 'employees_list'])->middleware('auth', 'deanery');
Route::add('GET', '/departaments_list', [Controller\DepartmentController::class, 'departaments_list'])->middleware('auth', 'deanery');
Route::add('GET', '/subjects_list', [Controller\SubjectController::class, 'subjects_list'])->middleware('auth', 'deanery');
Route::add(['GET', 'POST'], '/employees/{id}/edit', [Controller\EmployeeController::class, 'editEmployee'])->middleware('auth', 'deanery');
Route::add(['GET'], '/employee_search', [Controller\EmployeeController::class, 'employeeSearch'])->middleware('auth', 'deanery');
Route::add(['GET'], '/departament_search', [Controller\DepartmentController::class, 'departamentSearch'])->middleware('auth', 'deanery');
