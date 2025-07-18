<?php

declare(strict_types=1);

namespace JustSteveKing\Resume\Concerns;

use InvalidArgumentException;

trait ValidatesUrl
{
    protected function assertUrl(?string $url): void
    {
        if (null === $url) {
            return; // null URLs are allowed
        }

        if ('' === mb_trim($url)) {
            throw new InvalidArgumentException('URL cannot be empty');
        }

        if ( ! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("Invalid URL format: {$url}");
        }

        $parsed = parse_url($url);
        if ( ! is_array($parsed) || ! isset($parsed['scheme']) || ! in_array($parsed['scheme'], ['http', 'https'])) {
            throw new InvalidArgumentException("URL must have a valid scheme (http, https): {$url}");
        }
    }

    protected function assertHttpUrl(?string $url): void
    {
        if (null === $url) {
            return;
        }

        $this->assertUrl($url);

        $parsed = parse_url($url);
        /** @phpstan-ignore-next-line  */
        if ( ! in_array($parsed['scheme'], ['http', 'https'])) {
            throw new InvalidArgumentException("URL must use HTTP or HTTPS: {$url}");
        }
    }
}
