<?php

declare(strict_types=1);

namespace JustSteveKing\Resume\Concerns;

use InvalidArgumentException;

trait ValidatesEmail
{
    protected function assertEmail(?string $email): void
    {
        if (null === $email) {
            return; // null emails are allowed
        }

        if ('' === mb_trim($email)) {
            throw new InvalidArgumentException('Email cannot be empty');
        }

        // Additional validation for common issues
        if (mb_strlen($email) > 254) {
            throw new InvalidArgumentException('Email address is too long (max 254 characters)');
        }

        // Check for valid domain
        $parts = explode('@', $email);
        if (2 !== count($parts)) {
            throw new InvalidArgumentException("Invalid email format: {$email}");
        }

        [, $domain] = $parts;
        if ( ! $this->isValidDomain($domain)) {
            throw new InvalidArgumentException("Invalid email domain: {$domain}");
        }

        // Final check with filter_var
        if ( ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format: {$email}");
        }
    }

    private function isValidDomain(string $domain): bool
    {
        // Basic domain validation
        if (mb_strlen($domain) > 253) {
            return false;
        }

        // Check if domain has at least one dot
        if ( ! str_contains($domain, '.')) {
            return false;
        }

        // Check for valid characters
        return 1 === preg_match('/^[a-zA-Z0-9.-]+$/', $domain);
    }
}
