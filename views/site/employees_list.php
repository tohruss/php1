<div class="container">
    <h2>Список сотрудников</h2>
    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Имя</th>
            <th>Фамилия</th>
            <th>Отчество</th>
            <th>Пол</th>
            <th>Адрес</th>
            <th>Дата рождения</th>
            <th>Должность</th>
            <th>Кафедра</th>
            <th>Дисциплины</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($employees as $employee): ?>
            <tr>
                <td><?= htmlspecialchars($employee->id) ?></td>
                <td><?= htmlspecialchars($employee->first_name) ?></td>
                <td><?= htmlspecialchars($employee->last_name) ?></td>
                <td><?= htmlspecialchars($employee->middle_name ?? '-') ?></td>
                <td><?= $employee->gender == 1 ? 'Муж' : ($employee->gender == 2 ? 'Жен' : 'Не указан') ?></td>
                <td><?= htmlspecialchars($employee->address) ?></td>
                <td><?= date('d.m.Y', strtotime($employee->birth_date)) ?></td>
                <td><?= htmlspecialchars($employee->post) ?></td>
                <td>
                    <?php if ($employee->department): ?>
                        <?= htmlspecialchars($employee->department->name) ?>
                    <?php else: ?>
                        Не назначен
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($employee->subjects->isNotEmpty()): ?>
                        <ul class="subject-list">
                            <?php foreach ($employee->subjects as $subject): ?>
                                <li>
                                    <?= htmlspecialchars($subject->name) ?>
                                    (<?= date('H:i', strtotime($subject->pivot->hours)) ?> ч.)
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        Нет дисциплин
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (app()->auth->user()->role_id == 2): ?>
                        <a href="/employees/<?= $employee->id ?>/edit" class="btn btn-sm btn-warning">Редактировать</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>