<div class="container">
    <h2>Список кафедр</h2>
    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Создатель (логин)</th>
            <th>Время создания</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($departaments as $departament): ?>
            <tr>
                <td><?= htmlspecialchars($departament->id) ?></td>
                <td><?= htmlspecialchars($departament->name) ?></td>
                <td>
                    <?php if ($departament->creator): ?>
                        <?= htmlspecialchars($departament->creator->login) ?>
                    <?php else: ?>
                        Не указан
                    <?php endif; ?>
                </td>
                <td>
                    <?= $departament->create_at ? date('d.m.Y H:i', strtotime($departament->create_at)) : 'Нет данных' ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>