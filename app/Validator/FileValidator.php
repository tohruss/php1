<?php

namespace Validators;

use Src\Validator\AbstractValidator;

class FileValidator extends AbstractValidator
{
    protected string $message = 'Поле :field должно быть файлом';

    public function rule(): bool
    {
        return isset($this->value['error']) && $this->value['error'] === UPLOAD_ERR_OK;
    }
}