<div class="container-employee">
    <h2>Добавление нового сотрудника</h2>

    <?php if (isset($message)): ?>
        <div class="alert alert-danger"><?= $message ?></div>
    <?php endif; ?>

    <form method="post" class="employee-form">
        <div class="form-section">
            <h3>Учетные данные</h3>
            <div class="form-group">
                <label for="login">Логин:</label>
                <input type="text" id="login" name="login" required class="form-control">
            </div>
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required class="form-control">
            </div>
            <div class="form-group">
                <label for="role_id">Роль:</label>
                <select id="role_id" name="role_id" required class="form-control">
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role->id ?>"><?= $role->name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-section">
            <h3>Персональные данные</h3>
            <div class="form-group">
                <label for="last_name">Фамилия:</label>
                <input type="text" id="last_name" name="last_name" required class="form-control">
            </div>
            <div class="form-group">
                <label for="first_name">Имя:</label>
                <input type="text" id="first_name" name="first_name" required class="form-control">
            </div>
            <div class="form-group">
                <label for="middle_name">Отчество:</label>
                <input type="text" id="middle_name" name="middle_name" class="form-control">
            </div>
            <div class="form-group">
                <label>Пол:</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="gender" value="1" checked> Мужской
                    </label>
                    <label>
                        <input type="radio" name="gender" value="2"> Женский
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label for="birth_date">Дата рождения:</label>
                <input type="date" id="birth_date" name="birth_date" required class="form-control">
            </div>
            <div class="form-group">
                <label for="address">Адрес прописки:</label>
                <input type="text" id="address" name="address" required class="form-control">
            </div>
            <div class="form-group">
                <label for="position">Должность:</label>
                <input type="text" id="position" name="position" required class="form-control">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Зарегистрировать сотрудника</button>
    </form>
</div>