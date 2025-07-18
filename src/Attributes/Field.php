<?php

declare(strict_types=1);

namespace JustSteveKing\Resume\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class Field
{
    public function __construct(
        public string $name,
    ) {}
}
