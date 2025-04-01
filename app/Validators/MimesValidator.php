<?php

namespace Validators;

use Src\Validator\AbstractValidator;

class MimesValidator extends AbstractValidator
{
    protected string $message = 'Допустимые форматы: :values';

    public function rule(): bool
    {
        if (empty($this->args)) return false;

        $extension = strtolower(
            pathinfo($this->value['name'], PATHINFO_EXTENSION)
        );

        return in_array($extension, $this->args);
    }
}