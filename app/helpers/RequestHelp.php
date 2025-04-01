<?php

namespace helpers;

use Illuminate\Database\Capsule\Manager as Capsule;
use Collect\Validation;

class RequestHelp
{
    private static $errors = [];



    // Основной метод проверки данных
    public static function validate(array $data): array
    {
        self::$errors = [];

        // Проверка логина
        if (!self::checkLogin($data['login'] ?? '')) {
            self::$errors['login'][] = 'Логин уже занят или некорректен';
        }

        // Проверка пароля
        if (!self::checkPassword($data['password'] ?? '')) {
            self::$errors['password'][] = 'Пароль должен содержать минимум 8 символов, цифры и буквы';
        }

        // Проверка ФИО
        self::checkNameField('last_name', $data['last_name'] ?? '');
        self::checkNameField('first_name', $data['first_name'] ?? '');
        self::checkNameField('middle_name', $data['middle_name'] ?? '');

        // Проверка адреса
        if (!self::checkAddress($data['address'] ?? '')) {
            self::$errors['address'][] = 'Адрес содержит недопустимые символы';
        }

        // Проверка должности
        if (!self::checkPosition($data['post'] ?? '')) {
            self::$errors['post'] = ['Некорректное название должности'];
        }

        // Проверка часов
        if (!self::checkHours($data['hours'] ?? '00:00')) {
            self::$errors['hours'] = ['Формат часов должен быть ЧЧ:ММ'];
        }

        if (!self::checkAddress($data['address'] ?? '')) {
            self::$errors['address'][] = 'Адрес должен содержать только русские буквы, цифры и разрешенные символы (пробелы, запятые, точки, дефисы, №)';
        }
        // Проверка аватара
        self::validateAvatar($files['avatar'] ?? null);

        return self::$errors;
    }

    // Проверка логина на уникальность
    private static function checkLogin(string $login): bool
    {
        return !Capsule::table('users')->where('login', $login)->exists();
    }

    // Проверка пароля
    private static function checkPassword(string $password): bool
    {
        return preg_match(Validation::PASSWORD, $password);
    }

    // Проверка имени/фамилии/отчества
    private static function checkNameField(string $field, string $value): void
    {
        if (!preg_match(Validation::NAME_PATTERN, $value)) {
            self::$errors[$field][] = 'Допустимы только русские буквы, дефисы и апострофы';
        }
    }

    // Проверка адреса
    private static function checkAddress(string $address): bool
    {
        if (empty($address)) {
            self::$errors['address'][] = 'Адрес не может быть пустым';
            return false;
        }
        return preg_match(Validation::ADDRESS_PATTERN, $address);
    }

    // Проверка должности
    private static function checkPosition(string $position): bool
    {
        return preg_match(Validation::NAME_PATTERN, $position);
    }

    // Проверка часов
    private static function checkHours(string $hours): bool
    {
        return preg_match(Validation::HOURS_PATTERN, $hours);
    }

    private static function validateAvatar(?array $avatarFile): void
    {
        if ($avatarFile && $avatarFile['tmp_name']) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            $fileType = $avatarFile['type'];

            if (!in_array($fileType, $allowedTypes)) {
                self::$errors['avatar'][] = 'Допустимы только изображения в формате JPEG, PNG или JPG';
            }

            if ($avatarFile['size'] > 2 * 1024 * 1024) {
                self::$errors['avatar'][] = 'Размер файла не должен превышать 2MB';
            }
        }
    }

    public static function validateEdit(array $data, array $files = []): array
    {
        self::$errors = [];

        // Проверка должности
        if (!self::checkPosition($data['post'] ?? '')) {
            self::$errors['post'] = ['Некорректное название должности'];
        }

        // Проверка часов
        if (!self::checkHours($data['hours'] ?? '00:00')) {
            self::$errors['hours'] = ['Формат часов должен быть ЧЧ:ММ'];
        }

        // Проверка аватара
        self::validateAvatar($files['avatar'] ?? null);

        return self::$errors;
    }

}