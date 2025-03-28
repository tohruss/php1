<?php

namespace Controller;

use Src\View;

class UserController
{
    public function office(): string
    {
        $user = app()->auth->user();
        $roleName = $user->role->name ?? 'Пользователь';
        $employeeData = null;

        if ($user->role_id == 2 || $user->role_id == 3) {
            $user->load('employee');
            $employeeData = $user->employee;
        }

        return new View('site.office', [
            'message' => 'Добро пожаловать в личный кабинет',
            'user' => $user,
            'roleName' => $roleName,
            'employeeData' => $employeeData
        ]);
    }
}