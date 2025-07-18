<?php

declare(strict_types=1);

namespace JustSteveKing\Resume\DataObjects;

use JsonSerializable;
use JustSteveKing\Resume\Attributes\Field;
use JustSteveKing\Resume\Concerns\ValidatesEmail;
use JustSteveKing\Resume\Concerns\ValidatesUrl;

final readonly class Basics implements JsonSerializable
{
    use ValidatesEmail;
    use ValidatesUrl;

    /**
     * @param string $name
     * @param string $label
     * @param string|null $image
     * @param string|null $email
     * @param string|null $phone
     * @param string|null $url
     * @param string|null $summary
     * @param Location|null $location
     * @param list<Profile> $profiles
     */
    public function __construct(
        #[Field('name')]
        public string $name,
        #[Field('label')]
        public string $label,
        #[Field('image')]
        public ?string $image = null,
        #[Field('email')]
        public ?string $email = null,
        #[Field('phone')]
        public ?string $phone = null,
        #[Field('url')]
        public ?string $url = null,
        #[Field('summary')]
        public ?string $summary = null,
        #[Field('location')]
        public ?Location $location = null,
        #[Field('profiles')]
        public array $profiles = [],
    ) {
        if (null !== $this->email) {
            $this->assertEmail($this->email);
        }
        if (null !== $this->url) {
            $this->assertUrl($this->url);
        }
    }

    /**
     * Convert the Basics instance to an array for JSON serialization.
     *
     * @return array{
     *     name: string,
     *     label: string,
     *     image?: string|null,
     *     email?: string|null,
     *     phone?: string|null,
     *     url?: string|null,
     *     summary?: string|null,
     *     location?: array<string, mixed>|null,
     *     profiles: list<array<string, mixed>>
     * }
     */
    public function jsonSerialize(): array
    {
        $data = [
            'name' => $this->name,
            'label' => $this->label,
        ];

        if (null !== $this->email) {
            $data['email'] = $this->email;
        }
        if (null !== $this->phone) {
            $data['phone'] = $this->phone;
        }
        if (null !== $this->url) {
            $data['url'] = $this->url;
        }
        if (null !== $this->summary) {
            $data['summary'] = $this->summary;
        }
        if (null !== $this->location) {
            $data['location'] = $this->location->jsonSerialize();
        }

        $data['profiles'] = array_map(
            static fn($profile): array => $profile->jsonSerialize(),
            $this->profiles,
        );

        return $data;
    }
}
