<div class="office-container">
    <h2><?= $message ?></h2>
    <div class="user-info">
        <p><strong>Логин:</strong> <?= $user->login ?></p>
        <p><strong>Роль:</strong> <?= $roleName ?></p>
        <?php if ($user->isAdmin()): ?>
            <a href="<?= app()->route->getUrl('/create') ?>">Добавить сотрудника</a>
            <a href="<?= app()->route->getUrl('/employees_list') ?>">Список сотрудников</a>
            <a href="<?= app()->route->getUrl('/departaments_list') ?>">Список кафедр</a>
        <?php endif; ?>
    </div>
</div>

