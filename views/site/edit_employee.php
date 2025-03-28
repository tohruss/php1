<div class="container mt-4">
    <h2>Редактирование сотрудника: <?= htmlspecialchars($employee->getFullName()) ?></h2>

    <form method="POST" action="/employees/<?= $employee->id ?>/edit" enctype="multipart/form-data">
        <input name="csrf_token" type="hidden" value="<?= app()->auth::generateCSRF() ?>" />

        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $field => $messages): ?>
                    <?php if (!empty($messages)): ?>
                        <?php foreach ($messages as $message): ?>
                            <p><?= $message ?></p>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="mb-3">
            <label class="form-label">Аватар</label>
            <input type="file"
                   name="avatar"
                   accept="image/jpeg, image/png, image/jpg"
                   class="form-control <?= !empty($errors['avatar']) ? 'is-invalid' : '' ?>">

            <?php if (!empty($errors['avatar'])): ?>
                <div class="invalid-feedback">
                    <?= implode(', ', $errors['avatar']) ?>
                </div>
            <?php endif; ?>
            <?php if ($employee->user->avatar): ?>
                <div class="current-avatar mt-2">
                    <img src="/<?= htmlspecialchars($employee->user->avatar) ?>"
                         alt="Текущий аватар"
                         style="max-width: 200px;">
                </div>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label class="form-label">Должность</label>
            <input type="text" name="post"
                   class="form-control <?= !empty($errors['post']) ? 'is-invalid' : '' ?>"
                   value="<?= htmlspecialchars($old['post'] ?? $employee->post) ?>"
                   required>
            <?php if (!empty($errors['post'])): ?>
                <div class="invalid-feedback"><?= implode(', ', $errors['post']) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">Дисциплина</label>
            <?php if (!empty($errors['subject_id'])): ?>
                <div class="text-danger mb-2"><?= implode(', ', $errors['subject_id']) ?></div>
            <?php endif; ?>

            <?php foreach ($subjects as $subject): ?>
                <div class="form-check">
                    <input class="form-check-input <?= !empty($errors['subject_id']) ? 'is-invalid' : '' ?>"
                           type="radio"
                           name="subject_id"
                           value="<?= $subject->id ?>"
                        <?= ($old['subject_id'] ?? $employee->subjects->first()->id ?? null) == $subject->id ? 'checked' : '' ?>>
                    <label class="form-check-label">
                        <?= htmlspecialchars($subject->name) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">Часы</label>
            <input type="time" name="hours"
                   class="form-control <?= !empty($errors['hours']) ? 'is-invalid' : '' ?>"
                   value="<?= htmlspecialchars($old['hours'] ?? $hours ?? '00:00') ?>"
                   required step="1800">
            <?php if (!empty($errors['hours'])): ?>
                <div class="invalid-feedback"><?= implode(', ', $errors['hours']) ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Сохранить</button>
    </form>
</div>