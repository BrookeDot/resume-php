<?php

declare(strict_types=1);

namespace JustSteveKing\Resume\DataObjects;

use JsonSerializable;
use JustSteveKing\Resume\Attributes\Field;
use JustSteveKing\Resume\Concerns\ValidatesUrl;
use JustSteveKing\Resume\Enums\Network;

final readonly class Profile implements JsonSerializable
{
    use ValidatesUrl;

    /**
     * @param Network $network
     * @param string $username
     * @param string|null $url
     */
    public function __construct(
        #[Field('network')]
        public Network $network,
        #[Field('username')]
        public string $username,
        #[Field('url')]
        public ?string $url = null,
    ) {
        if ($this->url) {
            $this->assertUrl($this->url);
        }
    }

    /**
     * Convert the Profile instance to an array for JSON serialization.
     *
     * @return array{
     *     network: string,
     *     username: string,
     *     url?: string|null
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'network' => $this->network->value,
            'username' => $this->username,
            'url' => $this->url,
        ];
    }
}
