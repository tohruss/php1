<?php
use Model\User;
use PHPUnit\Framework\TestCase;
use Src\Request;
use Controller\EmployeeController;
class EmployeeTest extends TestCase
{
    protected static $testDepartments = [3, 6, 7, 8, 9];
    protected static $validRoles = [2, 3];
    protected static $genders = [1, 2];

    protected function setUp(): void
    {
        $_SERVER['DOCUMENT_ROOT'] = 'D:\XAMPP8.1\htdocs';

        $GLOBALS['app'] = new Src\Application(new Src\Settings([
            'app' => include $_SERVER['DOCUMENT_ROOT'] . '/config/app.php',
            'db' => include $_SERVER['DOCUMENT_ROOT'] . '/config/db.php',
            'path' => include $_SERVER['DOCUMENT_ROOT'] . '/config/path.php',
        ]));

        // Создание тестового администратора
        $admin = User::firstOrCreate(
            ['login' => 'dimasik_test'],
            [
                'password' => md5('taraskindima123'),
                'role_id' => 1,
            ]
        );


        // Авторизация администратора
        app()->auth->login($admin);
    }

    /**
     * @dataProvider createEmployeeProvider
     */
    public function testCreateEmployee(string $method, array $data, array $expected): void
    {
        $request = $this->createMock(Request::class);
        $request->method = $method;
        $request->expects($this->any())
            ->method('all')
            ->willReturn(array_merge($data, ['csrf_token' => 'mocked_token']));
        $controller = new EmployeeController();
        $response = $controller->createEmployee($request);

        // Проверка ошибок валидации
        if (!empty($expected['errors'])) {
            foreach ($expected['errors'] as $messages) {
                $this->assertStringContainsString($messages[0], $response);
            }
            return;
        }

        // Проверка редиректа
        $this->assertContains('Location: ' . $expected['redirect'], xdebug_get_headers());

        // Проверка создания в БД
        if ($expected['db_check']) {
            $user = User::where('login', $data['login'])->first();
            $this->assertNotNull($user);
            $this->assertNotNull($user->employee);

            // Проверка роли (должна быть 2 или 3)
            $this->assertContains($user->role_id, self::$validRoles);

            // Проверка кафедры (должна быть одна из допустимых)
            $department = $user->department;
            if ($department) {
                $this->assertContains($department->id, self::$testDepartments);
            }

            // Очистка тестовых данных
            $user->employee()->delete();
            $user->delete();
        }
    }

    public static function createEmployeeProvider(): array
    {
        return [
            // Пустые поля
            [
                'POST',
                [
                    'login' => '',
                    'password' => '',
                    'role_id' => '2',
                    'last_name' => '',
                    'first_name' => '',
                    'middle_name' => '',
                    'gender' => '1',
                    'birth_date' => '',
                    'address' => '',
                    'post' => '',
                    'department_id' => '',
                    'subject_id' => '',
                    'hours' => '',
                ],
                [
                    'errors' => [
                        'login' => ['Поле login пусто'],
                        'password' => ['Поле password пусто'],
                        'role_id' => ['Поле role_id пусто'],
                        'last_name' => ['Поле last_name пусто'],
                        'first_name' => ['Поле first_name пусто'],
                        'gender' => ['Поле gender пусто'],
                        'birth_date' => ['Поле birth_date пусто'],
                        'address' => ['Поле address пусто'],
                        'post' => ['Поле post пусто'],
                        'department_id' => ['Поле department_id пусто'],
                    ],
                    'redirect' => '',
                    'db_check' => false
                ]
            ],
            // Невалидные данные
            [
                'POST',
                [
                    'login' => 'test',
                    'password' => 'short',
                    'role_id' => 2,
                    'last_name' => 'Doe',
                    'first_name' => 'John',
                    'middle_name' => 'Smith',
                    'gender' => 2,
                    'birth_date' => '2050-01-01', // Дата в будущем
                    'address' => 'Some',
                    'post' => 'Dev',
                    'department_id' => 3,
                    'subject_id' => 20,
                    'hours' => '25:00' // Некорректное время
                ],
                [
                    'errors' => [
                        'password' => ['Поле password должно быть не менее 8 символов'],
                        'role_id' => ['Выбранная роль недопустима'],
                        'gender' => ['Выбранное значение для gender некорректно'],
                        'department_id' => ['Выбранная кафедра недопустима'],
                        'hours' => ['Некорректный формат часов']
                    ],
                    'redirect' => '',
                    'db_check' => false
                ]
            ],
            // Валидные данные - сотрудник деканата
            [
                'POST',
                [
                    'login' => 'test_dean_' . time(),
                    'password' => 'Validpassword123.',
                    'role_id' => 2, // Сотрудник деканата
                    'last_name' => 'Иванов',
                    'first_name' => 'Иван',
                    'middle_name' => 'Иванович',
                    'gender' => 1, // Мужской
                    'birth_date' => '1990-01-01',
                    'address' => 'ул. Пушкина',
                    'post' => 'Сотрудник деканата',
                    'department_id' => 3, // Допустимая кафедра
                    'subject_id' => 1,
                    'hours' => '10:00'
                ],
                [
                    'errors' => [],
                    'redirect' => '/employees_list',
                    'db_check' => true
                ]
            ],
            // Валидные данные - обычный сотрудник
            [
                'POST',
                [
                    'login' => 'test_emp_' . time(),
                    'password' => 'Anothervalid123.',
                    'role_id' => 3, // Сотрудник
                    'last_name' => 'Петрова',
                    'first_name' => 'Мария',
                    'middle_name' => 'Сергеевна',
                    'gender' => 2, // Женский
                    'birth_date' => '1985-05-15',
                    'address' => 'ул. Лермонтова',
                    'post' => 'Преподаватель',
                    'department_id' => 6, // Другая допустимая кафедра
                    'subject_id' => 1,
                    'hours' => '15:30'
                ],
                [
                    'errors' => [],
                    'redirect' => '/employees_list',
                    'db_check' => true
                ]
            ]
        ];
    }

}