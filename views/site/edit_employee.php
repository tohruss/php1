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

        <!-- Выбор существующей кафедры -->
        <div class="mb-3">
            <label class="form-label">Кафедра</label>
            <select name="department_id" class="form-select" required>
                <option value="">-- Выберите кафедру --</option>
                <?php foreach ($departaments as $department): ?>
                    <option value="<?= $department->id ?>"
                        <?= ($employee->user->department && $employee->user->department->id == $department->id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($department->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Радио-кнопки для дисциплин -->
        <div class="mb-3">
            <label class="form-label">Дисциплина</label>
            <?php foreach ($subjects as $subject): ?>
                <div class="form-check">
                    <input class="form-check-input" type="radio"
                           name="subject_id"
                           id="subject_<?= $subject->id ?>"
                           value="<?= $subject->id ?>"
                        <?= $employee->subjects->contains($subject->id) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="subject_<?= $subject->id ?>">
                        <?= htmlspecialchars($subject->name) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Поле для часов -->
        <div class="mb-3">
            <label class="form-label">Часы</label>
            <input type="number" name="hours" class="form-control"
                   value="<?= $employee->subjects->first()->pivot->hours ?? 0 ?>"
                   min="0" step="0.5" required>
        </div>

        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
    </form>
</div>