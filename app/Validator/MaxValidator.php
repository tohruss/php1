<?php

namespace Validators;

use Src\Validator\AbstractValidator;

class MaxValidator extends AbstractValidator
{
    protected string $message = 'Максимальный размер файла: :max КБ';

    public function rule(): bool
    {
        $maxSize = $this->args[0] * 1024; // Конвертация КБ в байты
        return $this->value['size'] <= $maxSize;
    }
}