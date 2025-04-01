<?php

namespace helpers;

class ResponseHelp
{
    // Форматирование ошибок для вывода
    public static function formatErrors(array $errors): array
    {
        $formatted = [];
        foreach ($errors as $field => $messages) {
            $formatted[$field] = implode(', ', $messages);
        }
        return $formatted;
    }

    // Перенаправление с ошибками
    public static function redirectWithErrors(string $url, array $errors, array $oldInput): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = $oldInput;
        header("Location: $url");
        exit();
    }


    // Получение ошибок из сессии
    public static function getSessionErrors(): array
    {
        return $_SESSION['errors'] ?? [];
    }

    // Очистка сессионных данных
    public static function clearSessionData(): void
    {
        unset($_SESSION['errors'], $_SESSION['old']);
    }
}