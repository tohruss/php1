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
            <th>Адрес проживания</th>
            <th>Дата рождения</th>
            <th>Должность</th>

        </tr>
        </thead>
        <tbody>
        <?php foreach ($employees as $employee): ?>
            <tr>
                <td><?= $employee->id ?></td>
                <td><?= $employee->first_name ?></td>
                <td><?= $employee->last_name ?></td>
                <td><?= $employee->middle_name ?></td>
                <td><?= $employee->gender == 1 ? 'Муж' : ($employee->gender == 2 ? 'Жен' : 'Не указан') ?></td>
                <td><?= $employee->address ?></td>
                <td><?= $employee->birth_date ?></td>
                <td><?= $employee->post ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
