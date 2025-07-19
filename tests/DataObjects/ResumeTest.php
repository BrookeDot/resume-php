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
    public function test_it_outputs_full_markdown(): void
    {
        $markdown = $this->buildCompleteResume()->toMarkdown();

        $this->assertStringContainsString('# John Doe', $markdown);
        $this->assertStringContainsString('## 💼 Work Experience', $markdown);
        $this->assertStringContainsString('## 🎓 Education', $markdown);
        $this->assertStringContainsString('## 🛠 Skills', $markdown);
        $this->assertStringContainsString('## 🌍 Languages', $markdown);
        $this->assertStringContainsString('Led development of core platform features', $markdown);
    }

    public function test_it_can_exclude_work_section(): void
    {
        $markdown = $this->buildCompleteResume()->toMarkdown([
            'work' => false,
        ]);

        $this->assertStringNotContainsString('## 💼 Work Experience', $markdown);
        $this->assertStringContainsString('# John Doe', $markdown);
    }

    public function test_it_can_output_only_basics(): void
    {
        $markdown = $this->buildCompleteResume()->toMarkdown([
            'basics' => true,
            'contact' => false,
            'profiles' => false,
            'work' => false,
            'education' => false,
            'skills' => false,
            'languages' => false,
        ]);

        $this->assertStringContainsString('# John Doe', $markdown);
        $this->assertStringContainsString('**Software Engineer**', $markdown);
        $this->assertStringNotContainsString('## 💼 Work Experience', $markdown);
        $this->assertStringNotContainsString('## 🎓 Education', $markdown);
    }

    public function test_it_handles_empty_sections_gracefully(): void
    {
        $emptyResume = new Resume(
            basics: new Basics(
                name: 'Jane Doe',
                label: 'Software Engineer',
                email: 'jane@example.com',
                url: 'https://janedoe.dev',
                profiles: [
                    new Profile(
                        network: Network::GitHub,
                        username: 'JaneDoe',
                        url: 'https://github.com/JaneDoe',
                    ),
                    new Profile(
                        network: Network::Twitter,
                        username: 'JaneDoe',
                        url: 'https://twitter.com/JaneDoe',
                    ),
                ],
            ),
        );

        $markdown = $emptyResume->toMarkdown();

        $this->assertStringContainsString('# Jane Doe', $markdown); // Changed from '# No Sections'
        $this->assertStringNotContainsString('## 💼 Work Experience', $markdown);
    }

    public function test_it_outputs_social_profiles(): void
    {
        $markdown = $this->buildCompleteResume()->toMarkdown([
            'include' => [
                'profiles' => true,
            ],
        ]);

        $this->assertStringContainsString('[github](https://github.com/johndoe)', $markdown);
    }

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
                    url: 'https://github.com/JaneDoe',
                ),
                new Profile(
                    network: Network::Twitter,
                    username: 'JaneDoe',
                    url: 'https://twitter.com/JaneDoe',
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
            schema: ResumeSchema::V1,
        );

        $result = $resume->toJsonLd();

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
            profiles: [],
        );

        $resume = new Resume(
            basics: $basics,
            skills: [],
            schema: ResumeSchema::V1,
        );

        $result = $resume->toJsonLd();

        $this->assertEmpty($result['sameAs']);
        $this->assertEmpty($result['knowsAbout']);
    }
}
