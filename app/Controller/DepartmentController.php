<?php

namespace Controller;

use Src\Request;
use Src\View;

class DepartmentController
{
    public function departaments_list(): string
    {
        // Загружаем кафедры вместе с создателями (только нужные поля)
        $departaments = \Model\Departament::with(['creator' => function ($query) {
            $query->select('id', 'login'); // Выбираем только id и login
        }])->get();

        return new View('site.departaments_list', [
            'departaments' => $departaments
        ]);
    }

    public function departamentSearch(Request $request): string
    {
        // Проверка прав (только для сотрудников деканата)
        if (!app()->auth->user()->isDeaneryEmployee()) { // Более читаемая проверка
            app()->route->redirect('/hello');
        }

        // Получаем выбранную кафедру из запроса
        $selectedDepartmentId = $request->get('departament_id');

        // Получаем список всех кафедр для выпадающего списка
        $departaments = \Model\Departament::orderBy('name')->get();

        // Если кафедра выбрана - ищем её сотрудников
        if ($selectedDepartmentId) {
            // Находим кафедру
            $departament = \Model\Departament::find($selectedDepartmentId);

            // Получаем сотрудников этой кафедры через отношение
            $employees = $departament ? $departament->employees()->with('user')->get() : collect();
        } else {
            $employees = collect();
        }

        return new View('site.departament_search', [
            'departaments' => $departaments,
            'employees' => $employees,
            'selectedDepartament' => $selectedDepartmentId
        ]);
    }

}