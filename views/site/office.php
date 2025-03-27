<div class="office-container">
    <h2><?= $message ?></h2>
    <div class="user-info">
        <p><strong>Логин:</strong> <?= $user->login ?></p>
        <p><strong>Роль:</strong> <?= $roleName ?></p>

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
            </div>
        <?php endif; ?>
    </div>
</div>