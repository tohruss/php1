<div class="office-container">
    <h2><?= $message ?></h2>
    <div class="user-info">
        <div class="office_user">
            <div class="avatar-container">
                <?php if ($user->avatar): ?>
                    <img src="/<?= htmlspecialchars($user->avatar) ?>"
                         alt="Аватар"
                         class="avatar-image">
                <?php else: ?>
                    <div class="avatar-placeholder">Нет аватара</div>
                <?php endif; ?>
            </div>
            <div>
                <p><strong>Логин:</strong> <?= $user->login ?></p>
                <p><strong>Роль:</strong> <?= $roleName ?></p>
            </div>
        </div>
        <?php if ($employeeData): ?>
            <div class="employee-info">
                <h3>Информация о сотруднике:</h3>
                <p><strong>ФИО:</strong>
                    <?= htmlspecialchars($employeeData->last_name) ?>
                    <?= htmlspecialchars($employeeData->first_name) ?>
                    <?= htmlspecialchars($employeeData->middle_name) ?>
                </p>
                <p><strong>Должность:</strong> <?= htmlspecialchars($employeeData->post) ?></p>
                <p><strong>Дата рождения:</strong> <?= date('d.m.Y', strtotime($employeeData->birth_date)) ?></p>
                <p><strong>Адрес:</strong> <?= htmlspecialchars($employeeData->address) ?></p>

                <!-- Добавленная информация о кафедре -->
                <p><strong>Кафедра:</strong>
                    <?php if ($employeeData->department): ?>
                        <?= htmlspecialchars($employeeData->department->name) ?>
                    <?php else: ?>
                        Не назначена
                    <?php endif; ?>
                </p>

                <!-- Добавленная информация о дисциплинах -->
                <p><strong>Дисциплины:</strong></p>
                <?php if ($employeeData->subjects->isNotEmpty()): ?>
                    <ul class="subject-list">
                        <?php foreach ($employeeData->subjects as $subject): ?>
                            <li>
                                <?= htmlspecialchars($subject->name) ?>
                                (<?= date('H:i', strtotime($subject->pivot->hours)) ?> ч.)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Нет назначенных дисциплин</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($user->isAdmin() || $user->role_id == 2): ?>
            <div class="button-control">
                <?php if ($user->isAdmin()): ?>
                    <a href="<?= app()->route->getUrl('/create') ?>">Добавить сотрудника</a>
                <?php endif; ?>
                <a href="<?= app()->route->getUrl('/employees_list') ?>">Список сотрудников</a>
                <a href="<?= app()->route->getUrl('/departaments_list') ?>">Список кафедр</a>
                <a href="<?= app()->route->getUrl('/subjects_list') ?>">Список дисциплин</a>
                <?php if ($user->role_id == 2): ?>
                    <a href="<?= app()->route->getUrl('/employee_search') ?>" class="btn btn-primary">Поиск дисциплины</a>
                    <a href="<?= app()->route->getUrl('/departament_search') ?>" class="btn btn-primary">Поиск кафедры</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>