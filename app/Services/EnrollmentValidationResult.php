<?php

namespace App\Services;

class EnrollmentValidationResult
{
    public function __construct(
        public readonly bool $valid,
        public readonly array $errors
    ) {}
    
    public function isValid(): bool
    {
        return $this->valid;
    }
    
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    public function getFirstError(): ?string
    {
        return $this->errors[0] ?? null;
    }
    
    public function hasError(string $errorType): bool
    {
        return collect($this->errors)->contains(fn($error) => str_contains($error, $errorType));
    }
    
    public function getErrorsAsString(): string
    {
        return implode('; ', $this->errors);
    }
}