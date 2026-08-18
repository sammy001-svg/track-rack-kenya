<?php
namespace App\Core;

/**
 * Minimal rule-based validator.
 *
 *   $v = new Validator($_POST);
 *   $v->require('name', 'Full name')->max('name', 150)
 *     ->require('email', 'Email')->email('email');
 *   if ($v->fails()) { ... $v->errors() ... }
 */
class Validator
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function value(string $field, $default = null)
    {
        $value = $this->data[$field] ?? $default;
        return is_string($value) ? trim($value) : $value;
    }

    private function addError(string $field, string $message): void
    {
        // Keep only the first error per field - the form shows one line.
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    private function isBlank(string $field): bool
    {
        $value = $this->value($field);
        return $value === null || $value === '' || (is_array($value) && $value === []);
    }

    public function require(string $field, string $label): self
    {
        if ($this->isBlank($field)) {
            $this->addError($field, "{$label} is required.");
        }
        return $this;
    }

    public function email(string $field, string $label = 'Email address'): self
    {
        if (!$this->isBlank($field) && !filter_var($this->value($field), FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "{$label} does not look like a valid email address.");
        }
        return $this;
    }

    public function phone(string $field, string $label = 'Phone number'): self
    {
        if (!$this->isBlank($field)) {
            $digits = preg_replace('/\D+/', '', (string) $this->value($field));
            if (strlen($digits) < 7) {
                $this->addError($field, "{$label} does not look like a valid number.");
            }
        }
        return $this;
    }

    public function min(string $field, int $length, string $label): self
    {
        if (!$this->isBlank($field) && mb_strlen((string) $this->value($field)) < $length) {
            $this->addError($field, "{$label} must be at least {$length} characters.");
        }
        return $this;
    }

    public function max(string $field, int $length, string $label): self
    {
        if (!$this->isBlank($field) && mb_strlen((string) $this->value($field)) > $length) {
            $this->addError($field, "{$label} must be {$length} characters or fewer.");
        }
        return $this;
    }

    public function numeric(string $field, string $label): self
    {
        if (!$this->isBlank($field) && !is_numeric($this->value($field))) {
            $this->addError($field, "{$label} must be a number.");
        }
        return $this;
    }

    public function in(string $field, array $allowed, string $label): self
    {
        if (!$this->isBlank($field) && !in_array($this->value($field), $allowed, true)) {
            $this->addError($field, "{$label} is not a valid choice.");
        }
        return $this;
    }

    public function matches(string $field, string $otherField, string $label): self
    {
        if ($this->value($field) !== $this->value($otherField)) {
            $this->addError($field, "{$label} do not match.");
        }
        return $this;
    }

    /** Reject submissions where a hidden honeypot field was filled in. */
    public function honeypot(string $field = 'website'): self
    {
        if (!$this->isBlank($field)) {
            $this->addError('_spam', 'Your submission was flagged as automated.');
        }
        return $this;
    }

    public function addManualError(string $field, string $message): self
    {
        $this->addError($field, $message);
        return $this;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function passes(): bool
    {
        return $this->errors === [];
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        return $this->errors === [] ? null : reset($this->errors);
    }
}
