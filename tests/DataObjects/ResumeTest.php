<?php

declare(strict_types=1);

namespace DataObjects;

use JustSteveKing\Resume\DataObjects\Basics;
use JustSteveKing\Resume\DataObjects\Profile;
use JustSteveKing\Resume\DataObjects\Resume;
use JustSteveKing\Resume\DataObjects\Skill;
use JustSteveKing\Resume\Enums\Network;
use JustSteveKing\Resume\Enums\ResumeSchema;
use Tests\PackageTestCase;

final class ResumeTest extends PackageTestCase
{
    public function testTransformReturnsCorrectJsonLdStructure(): void
    {
        $basics = new Basics(
            name: 'Jane Doe',
            label: 'Software Engineer',
            email: 'jane@example.com',
            url: 'https://janedoe.dev',
            profiles: [
                new Profile(
                    network: Network::GitHub,
                    username: 'JaneDoe',
                    url: 'https://github.com/JaneDoe'
                ),
                new Profile(
                    network: Network::Twitter,
                    username: 'JaneDoe',
                    url: 'https://twitter.com/JaneDoe'
                ),
            ],
        );

        $skills = [
            new Skill(name: 'PHP'),
            new Skill(name: 'JavaScript'),
        ];

        $resume = new Resume(
            basics: $basics,
            skills: $skills,
            schema: ResumeSchema::V1
        );

        $result = $resume->toJsonLd($resume);

        $this->assertIsArray($result);
        $this->assertSame('https://schema.org', $result['@context']);
        $this->assertSame('Person', $result['@type']);
        $this->assertSame('Jane Doe', $result['name']);
        $this->assertSame('https://janedoe.dev', $result['url']);
        $this->assertSame('Software Engineer', $result['jobTitle']);
        $this->assertEquals([
            'https://github.com/JaneDoe',
            'https://twitter.com/JaneDoe',
        ], $result['sameAs']);
        $this->assertEquals(['PHP', 'JavaScript'], $result['knowsAbout']);
    }

    public function testTransformHandlesMissingProfilesAndSkills(): void
    {
        $basics = new Basics(
            name: 'John Smith',
            label: 'Developer',
            email: 'john@example.com',
            url: 'https://johnsmith.dev',
            profiles: []
        );

        $resume = new Resume(
            basics: $basics,
            skills: [],
            schema: ResumeSchema::V1
        );

        $result = $resume->toJsonLd($resume);

        $this->assertEmpty($result['sameAs']);
        $this->assertEmpty($result['knowsAbout']);
    }
}
