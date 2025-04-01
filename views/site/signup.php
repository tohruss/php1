<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $field => $messages): ?>
            <?php foreach ($messages as $message): ?>
                <?= str_replace(':field', $field, $message) ?><br>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post">
    <input name="csrf_token" type="hidden" value="<?= app()->auth::generateCSRF() ?>"/>

    <label>Логин
        <input type="text" name="login" value="<?= $old['login'] ?? '' ?>">
        <?php if (isset($errors['login'])): ?>
            <span class="error"><?= implode(', ', $errors['login']) ?></span>
        <?php endif; ?>
    </label>

    <label>Пароль
        <input type="password" name="password">
        <?php if (isset($errors['password'])): ?>
            <span class="error"><?= implode(', ', $errors['password']) ?></span>
        <?php endif; ?>
    </label>

    <label style="display: block">
        <input type="checkbox" name="is_admin" value="1" <?= isset($old['is_admin']) ? 'checked' : '' ?>>
        Зарегистрировать как администратора
    </label>

    <button>Зарегистрироваться</button>
</form>