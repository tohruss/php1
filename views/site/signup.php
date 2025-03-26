<h2>Регистрация нового пользователя</h2>
<h3><?= $message ?? ''; ?></h3>
<form method="post">
    <label>Логин <input type="text" name="login"></label>
    <label>Пароль <input type="password" name="password"></label>
    <label style="display: block">
        <input type="checkbox" name="is_admin" value="1">
        Зарегистрировать как администратора
    </label>
    <button>Зарегистрироваться</button>
</form>
