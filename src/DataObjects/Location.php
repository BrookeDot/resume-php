<?php

declare(strict_types=1);

namespace JustSteveKing\Resume\DataObjects;

use JsonSerializable;
use JustSteveKing\Resume\Attributes\Field;

final readonly class Location implements JsonSerializable
{
    /**
     * Create a new Location instance.
     *
     * @param string|null $address The street address.
     * @param string|null $postalCode The postal code.
     * @param string|null $city The city name.
     * @param string|null $countryCode The country code (ISO 3166-1 alpha-2).
     * @param string|null $region The region or state name.
     */
    public function __construct(
        #[Field('address')]
        public ?string $address = null,
        #[Field('postalCode')]
        public ?string $postalCode = null,
        #[Field('city')]
        public ?string $city = null,
        #[Field('countryCode')]
        public ?string $countryCode = null,
        #[Field('region')]
        public ?string $region = null,
    ) {}

    /**
     * Convert the Location instance to an array for JSON serialization.
     *
     * @return array{
     *     address?: string|null,
     *     postalCode?: string|null,
     *     city?: string|null,
     *     countryCode?: string|null,
     *     region?: string|null
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'address' => $this->address,
            'postalCode' => $this->postalCode,
            'city' => $this->city,
            'countryCode' => $this->countryCode,
            'region' => $this->region,
        ];
    }
}
