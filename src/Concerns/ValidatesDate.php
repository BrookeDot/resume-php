<?php

declare(strict_types=1);

namespace JustSteveKing\Resume\Concerns;

use InvalidArgumentException;

trait ValidatesDate
{
    protected function assertDate(?string $date): void
    {
        if (null === $date) {
            return;
        }

        if ( ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            throw new InvalidArgumentException("Date must be in YYYY-MM-DD format: {$date}");
        }

        if ( ! strtotime($date)) {
            throw new InvalidArgumentException("Invalid date: {$date}");
        }
    }
}
