<?php
namespace App\Core;

class Validator {
    private array $errors = [];
    private array $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function required(string $field, string $label = ''): self {
        $label = $label ?: $field;
        $value = $this->data[$field] ?? '';
        if (trim((string)$value) === '') {
            $this->errors[$field] = "{$label} est requis.";
        }
        return $this;
    }

    public function email(string $field, string $label = ''): self {
        $label = $label ?: $field;
        $value = $this->data[$field] ?? '';
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "{$label} n'est pas un email valide.";
        }
        return $this;
    }

    public function minLength(string $field, int $min, string $label = ''): self {
        $label = $label ?: $field;
        $value = $this->data[$field] ?? '';
        if (strlen((string)$value) < $min) {
            $this->errors[$field] = "{$label} doit faire au moins {$min} caractères.";
        }
        return $this;
    }

    public function maxLength(string $field, int $max, string $label = ''): self {
        $label = $label ?: $field;
        $value = $this->data[$field] ?? '';
        if (strlen((string)$value) > $max) {
            $this->errors[$field] = "{$label} ne doit pas dépasser {$max} caractères.";
        }
        return $this;
    }

    public function numeric(string $field, string $label = ''): self {
        $label = $label ?: $field;
        $value = $this->data[$field] ?? '';
        if ($value !== '' && !is_numeric($value)) {
            $this->errors[$field] = "{$label} doit être un nombre.";
        }
        return $this;
    }

    public function inArray(string $field, array $allowed, string $label = ''): self {
        $label = $label ?: $field;
        $value = $this->data[$field] ?? '';
        if ($value !== '' && !in_array($value, $allowed, true)) {
            $this->errors[$field] = "{$label} contient une valeur invalide.";
        }
        return $this;
    }

    public function matches(string $field, string $otherField, string $label = '', string $otherLabel = ''): self {
        $label = $label ?: $field;
        $otherLabel = $otherLabel ?: $otherField;
        if (($this->data[$field] ?? '') !== ($this->data[$otherField] ?? '')) {
            $this->errors[$field] = "{$label} ne correspond pas à {$otherLabel}.";
        }
        return $this;
    }

    public function passes(): bool {
        return empty($this->errors);
    }

    public function errors(): array {
        return $this->errors;
    }

    public function firstError(): string {
        return $this->errors ? reset($this->errors) : '';
    }
}
