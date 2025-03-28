<h2>Регистрация нового пользователя</h2>
<form method="post">
    <input name="csrf_token" type="hidden" value="<?= app()->auth::generateCSRF() ?>"/>
    <?php if (isset($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $field => $messages): ?>
                <?php foreach ($messages as $message): ?>
                    <p class="error"><?= $message ?></p>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>


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