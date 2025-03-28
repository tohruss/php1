<div class="container mt-4">
    <h2>Редактирование сотрудника: <?= htmlspecialchars($employee->getFullName()) ?></h2>

    <form method="POST" action="/employees/<?= $employee->id ?>/edit">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Поле должности -->
        <div class="mb-3">
            <label class="form-label">Должность</label>
            <input type="text" name="post" class="form-control"
                   value="<?= htmlspecialchars($old['post'] ?? $employee->post) ?>" required>
        </div>

        <!-- Дисциплины -->
        <div class="mb-3">
            <label class="form-label">Дисциплина</label>
            <?php foreach ($subjects as $subject): ?>
                <div class="form-check">
                    <input class="form-check-input" type="radio"
                           name="subject_id"
                           value="<?= $subject->id ?>"
                        <?= $employee->subjects->contains($subject->id) ? 'checked' : '' ?>>
                    <label class="form-check-label">
                        <?= htmlspecialchars($subject->name) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Поле часов -->
        <div class="mb-3">
            <label class="form-label">Часы</label>
            <input type="time" name="hours"
                   value="<?= htmlspecialchars($hours ?? '00:00') ?>"
                   required step="1800">
        </div>

        <button type="submit" class="btn btn-primary">Сохранить</button>
    </form>
</div>