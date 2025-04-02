<?php

use Model\User;
use PHPUnit\Framework\TestCase;

class SiteTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     */
    public function testSignup(string $httpMethod, array $userData, string $message): void
    {
        //Выбираем занятый логин из базы данных
        if ($userData['login'] === 'dimasik33') {
            $userData['login'] = User::get()->first()->login;
        }

        // Создаем заглушку для класса Request.
        $request = $this->createMock(\Src\Request::class);
        // Переопределяем метод all() и свойство method
        $request->expects($this->any())
            ->method('all')
            ->willReturn(array_merge($userData, ['csrf_token' => 'mocked_token']));
        $request->method = $httpMethod;

        //Сохраняем результат работы метода в переменную
        $result = (new \Controller\Site())->signup($request);

        if (!empty($result)) {
            //Проверяем варианты с ошибками валидации
            $message = '/' . preg_quote($message, '/') . '/';
            $this->expectOutputRegex($message);
            return;
        }

        //Проверяем добавился ли пользователь в базу данных
        $this->assertTrue((bool)User::where('login', $userData['login'])->count());
        //Удаляем созданного пользователя из базы данных
        User::where('login', $userData['login'])->delete();

        //Проверяем редирект при успешной регистрации
        $this->assertContains($message, xdebug_get_headers());
    }


    //Настройка конфигурации окружения
    protected function setUp(): void
    {
        $_SERVER['DOCUMENT_ROOT'] = 'D:\XAMPP8.1\htdocs';

        $GLOBALS['app'] = new Src\Application(new Src\Settings([
            'app' => include $_SERVER['DOCUMENT_ROOT'] . '/config/app.php',
            'db' => include $_SERVER['DOCUMENT_ROOT'] . '/config/db.php',
            'path' => include $_SERVER['DOCUMENT_ROOT'] . '/config/path.php',
        ]));

        // Создание тестового пользователя, если не существует
        if (!User::where('login', 'dimasik')->exists()) {
            User::create([
                'login' => 'dimasik',
                'password' => 'tohrutop311www',
                'role_id' => 1,
            ]);
        }

        if (!function_exists('app')) {
            function app()
            {
                return $GLOBALS['app'];
            }
        }
    }
//Метод, возвращающий набор тестовых данных
    public static function additionProvider(): array
    {
        return [
            ['GET', ['login' => '', 'password' => ''],
                '<h3></h3>'
            ],
            ['POST', [
                'login' => '',
                'password' => '',
                'csrf_token' => 'mocked_token'
            ], '<h3>{"login":["Поле login пусто"],"password":["Поле password пусто"]}</h3>'],
            ['POST', [
                'login' => 'dimasik33',
                'password' => 'tohrutop311www',
                'csrf_token' => 'mocked_token'
            ], '<h3>{"login":["Поле login должно быть уникально"]}</h3>'],
            ['POST', [
                'login' => md5(time()),
                'password' => 'tohrutop311www',
                'csrf_token' => 'mocked_token',
                'role_id' => 1
            ], 'Location: /login']
        ];
    }
    /**
     * @dataProvider loginProvider
     */
    public function testLogin(string $httpMethod, array $userData, string $message): void
    {
        $request = $this->createMock(\Src\Request::class);
        $request->expects($this->any())
            ->method('all')
            ->willReturn(array_merge($userData, ['csrf_token' => 'mocked_token']));
        $request->method = $httpMethod;

        $result = (new \Controller\Site())->login($request);

        if (!empty($result)) {
            $message = '/' . preg_quote($message, '/') . '/';
            $this->expectOutputRegex($message);
            echo $result;
            return;
        }

        $this->assertContains($message, xdebug_get_headers());
    }

    public static function loginProvider(): array
    {
        return [
            ['POST', ['login' => '', 'password' => ''], '<h3>{"login":["Поле login пусто"],"password":["Поле password пусто"]}</h3>'],
            ['POST', ['login' => 'wrong', 'password' => 'wrong'], '<h3>Вы ввели некорректные данные</h3>'],
            ['POST', ['login' => 'dimasik', 'password' => 'taraskindima123'], 'Location: /office'],
        ];
    }

}
