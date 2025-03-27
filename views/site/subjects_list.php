<div class="container">
    <h2><?= $message ?? ''; ?></h2>

    <table class="table">
        <thead>
        <tr>
            <th>ID</th>
            <th>Название дисциплины</th>
            <th>Дата создания</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($subjects as $subject): ?>
            <tr>
                <td><?= $subject->id ?></td>
                <td><?= htmlspecialchars($subject->name) ?></td>
                <td><?= $subject->create_at ? date('d.m.Y H:i', strtotime($subject->create_at)) : 'Не указано' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
