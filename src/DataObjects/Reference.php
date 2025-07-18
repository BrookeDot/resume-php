<?php

declare(strict_types=1);

namespace JustSteveKing\Resume\DataObjects;

use JsonSerializable;
use JustSteveKing\Resume\Attributes\Field;

final readonly class Reference implements JsonSerializable
{
    /**
     * Create a new Reference instance.
     *
     * @param string $name The name of the reference.
     * @param string $reference The reference details, such as contact information.
     */
    public function __construct(
        #[Field('name')]
        public string $name,
        #[Field('reference')]
        public string $reference,
    ) {}

    /**
     * Convert the Reference instance to an array for JSON serialization.
     *
     * @return array{
     *     name: string,
     *     reference: string,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'reference' => $this->reference,
        ];
    }
}
