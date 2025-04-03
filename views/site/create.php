<div class="container-employee">
    <h2>Добавление нового сотрудника</h2>

    <?php if (isset($message)): ?>
        <div class="alert alert-danger"><?= $message ?></div>
    <?php endif; ?>

    <form method="post" class="employee-form">
        <input name="csrf_token" type="hidden" value="<?= app()->auth::generateCSRF() ?>"/>
        <div class="form-section">
            <h3>Учетные данные</h3>
            <div class="form-group">
                <label for="login">Логин:</label>
                <input type="text" id="login" name="login"
                       value="<?= htmlspecialchars($old['login'] ?? '') ?>"
                       class="form-control <?= isset($errors['login']) ? 'is-invalid' : '' ?>">
                <?php if (isset($errors['login'])): ?>
                    <div style="color: red" class="invalid-feedback"><?= implode(', ', $errors['login']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password"
                       class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>">
                <?php if (isset($errors['password'])): ?>
                    <div style="color: red" class="invalid-feedback"><?= implode(', ', $errors['password']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="role_id">Роль:</label>
                <select id="role_id" name="role_id"
                        class="form-control <?= isset($errors['role_id']) ? 'is-invalid' : '' ?>">
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role->id ?>"
                            <?= ($old['role_id'] ?? '') == $role->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($role->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['role_id'])): ?>
                    <div style="color: red" class="invalid-feedback"><?= implode(', ', $errors['role_id']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Персональные данные -->
        <div class="form-section">
            <h3>Персональные данные</h3>

            <div class="form-group">
                <label for="last_name">Фамилия:</label>
                <input type="text" id="last_name" name="last_name"
                       value="<?= htmlspecialchars($old['last_name'] ?? '') ?>"
                       class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>">
                <?php if (isset($errors['last_name'])): ?>
                    <div style="color: red" class="invalid-feedback"><?= implode(', ', $errors['last_name']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="first_name">Имя:</label>
                <input type="text" id="first_name" name="first_name"
                       value="<?= htmlspecialchars($old['first_name'] ?? '') ?>"
                       class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>">
                <?php if (isset($errors['first_name'])): ?>
                    <div style="color: red" class="invalid-feedback"><?= implode(', ', $errors['first_name']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="middle_name">Отчество:</label>
                <input type="text" id="middle_name" name="middle_name"
                       value="<?= htmlspecialchars($old['middle_name'] ?? '') ?>"
                       class="form-control <?= isset($errors['middle_name']) ? 'is-invalid' : '' ?>">
                <?php if (isset($errors['middle_name'])): ?>
                    <div style="color: red" class="invalid-feedback"><?= implode(', ', $errors['middle_name']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Пол:</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="gender" value="1"
                            <?= ($old['gender'] ?? 1) == 1 ? 'checked' : '' ?>> Мужской
                    </label>
                    <label>
                        <input type="radio" name="gender" value="2"
                            <?= ($old['gender'] ?? 1) == 2 ? 'checked' : '' ?>> Женский
                    </label>
                </div>
                <?php if (isset($errors['gender'])): ?>
                    <div style="color: red" class="text-danger"><?= implode(', ', $errors['gender']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="birth_date">Дата рождения:</label>
                <input type="date" id="birth_date" name="birth_date"
                       value="<?= htmlspecialchars($old['birth_date'] ?? '') ?>"
                       max="<?= date('Y-m-d') ?>"
                       class="form-control <?= isset($errors['birth_date']) ? 'is-invalid' : '' ?>">
                <?php if (isset($errors['birth_date'])): ?>
                    <div style="color: red" class="invalid-feedback"><?= implode(', ', $errors['birth_date']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="address">Адрес прописки:</label>
                <input type="text" id="address" name="address"
                       value="<?= htmlspecialchars($old['address'] ?? '') ?>"
                       class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>">
                <?php if (isset($errors['address'])): ?>
                    <div style="color: red" class="invalid-feedback"><?= implode(', ', $errors['address']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="post">Должность:</label>
                <input type="text" id="post" name="post"
                       value="<?= htmlspecialchars($old['post'] ?? '') ?>"
                       class="form-control <?= isset($errors['post']) ? 'is-invalid' : '' ?>">
                <?php if (isset($errors['post'])): ?>
                    <div style="color: red" class="invalid-feedback"><?= implode(', ', $errors['post']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Рабочее место -->
        <div class="form-section">
            <h3>Рабочее место</h3>

            <div class="form-group">
                <label for="department_id">Кафедра:</label>
                <select id="department_id" name="department_id"
                        class="form-control <?= isset($errors['department_id']) ? 'is-invalid' : '' ?>">
                    <option value="">-- Выберите кафедру --</option>
                    <?php foreach ($departaments as $departament): ?>
                        <option value="<?= $departament->id ?>"
                            <?= ($old['department_id'] ?? '') == $departament->id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($departament->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['department_id'])): ?>
                    <div style="color: red" class="invalid-feedback"><?= implode(', ', $errors['department_id']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Дисциплины:</label>
                <?php if (isset($errors['subject_id'])): ?>
                    <div class="text-danger mb-2"><?= implode(', ', $errors['subject_id']) ?></div>
                <?php endif; ?>
                <div class="subjects-list">
                    <?php foreach ($subjects as $subject): ?>
                        <div class="subject-item">
                            <label class="subject-radio">
                                <input type="radio" name="subject_id" value="<?= $subject->id ?>"
                                    <?= ($old['subject_id'] ?? '') == $subject->id ? 'checked' : '' ?>>
                                <?= htmlspecialchars($subject->name) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="hours-input mt-3">
                    <label>Часы:</label>
                    <input type="time" name="hours"
                           value="<?= htmlspecialchars($old['hours'] ?? '00:00') ?>"
                           class="<?= isset($errors['hours']) ? 'is-invalid' : '' ?>">
                    <?php if (isset($errors['hours'])): ?>
                        <div style="color: red" class="invalid-feedback"><?= implode(', ', $errors['hours']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <button class="btn btn-primary">Зарегистрировать сотрудника</button>
    </form>
</div>