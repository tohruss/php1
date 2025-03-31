<?php

namespace Controller;

use Src\View;

class SubjectController
{
    public function subjects_list(): string
    {
        $user = app()->auth->user();

        // Проверка прав доступа (админ или сотрудник деканата)
        if (!$user->isAdmin() && !$user->isDeaneryEmployee()) {
            app()->route->redirect('/hello');
        }

        $subjects = \Model\Subject::all();

        return new View('site.subjects_list', [
            'subjects' => $subjects,
            'message' => 'Список дисциплин'
        ]);
    }


}