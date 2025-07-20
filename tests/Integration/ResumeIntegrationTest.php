<?php

declare(strict_types=1);

namespace Tests\Integration;

use JustSteveKing\Resume\Builders\ResumeBuilder;
use JustSteveKing\Resume\DataObjects\Basics;
use JustSteveKing\Resume\DataObjects\Resume;
use JustSteveKing\Resume\DataObjects\Skill;
use JustSteveKing\Resume\DataObjects\Work;
use JustSteveKing\Resume\Enums\SkillLevel;
use Tests\PackageTestCase;

final class ResumeIntegrationTest extends PackageTestCase
{
    public function test_complete_resume_workflow(): void
    {
        // Step 1: Build a complete resume using the builder
        $resume = $this->buildCompleteResume();

        // Step 2: Serialize to JSON
        $json = json_encode($resume, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        $this->assertIsString($json);
        $this->assertNotEmpty($json);

        // Step 3: Deserialize back to array
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($data);

        // Step 4: Build a second resume with the same data
        $secondResume = $this->buildCompleteResume();
        $this->assertInstanceOf(Resume::class, $secondResume);

        // Step 5: Verify the second resume matches the original
        $this->assertResumesAreEqual($resume, $secondResume);

        // Step 6: Test that JSON output is identical
        $originalJson = json_encode($resume, JSON_THROW_ON_ERROR);
        $secondJson = json_encode($secondResume, JSON_THROW_ON_ERROR);
        $this->assertJsonStringEqualsJsonString($originalJson, $secondJson);
    }

    public function test_resume_summary_workflow(): void
    {
        $resume = $this->buildCompleteResume();
        $summary = $resume->getSummary();

        $expectedSummary = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'work_experiences' => 2,
            'education_entries' => 1,
            'skills' => 3,
            'projects' => 1,
            'languages' => 2,
            'has_volunteer_experience' => true,
            'has_awards' => true,
            'has_publications' => true,
        ];

        $this->assertSame($expectedSummary, $summary);
    }

    public function test_json_resume_schema_compliance(): void
    {
        $resume = $this->buildCompleteResume();
        $json = json_encode($resume, JSON_THROW_ON_ERROR);
        $data = json_decode($json, true, JSON_THROW_ON_ERROR);

        // Test required fields according to JSON Resume schema
        $this->assertArrayHasKey('$schema', $data);
        $this->assertSame('https://jsonresume.org/schema/schema.json', $data['$schema']);

        $this->assertArrayHasKey('basics', $data);
        $this->assertArrayHasKey('name', $data['basics']);
        $this->assertArrayHasKey('label', $data['basics']);

        // Test optional sections
        $optionalSections = [
            'work', 'volunteer', 'education', 'awards',
            'certificates', 'publications', 'skills',
            'languages', 'interests', 'references', 'projects',
        ];

        foreach ($optionalSections as $section) {
            if (isset($data[$section])) {
                $this->assertIsArray($data[$section]);
            }
        }
    }

    public function test_performance_with_large_resume(): void
    {
        $startTime = microtime(true);

        // Build a résumé with many entries
        $resume = new ResumeBuilder()
            ->basics(new Basics(
                name: 'John Doe',
                label: 'Software Engineer',
                email: 'john@example.com',
            ));

        // Add many work experiences
        for ($i = 0; $i < 50; $i++) {
            $resume->addWork(new Work(
                name: "Company {$i}",
                position: "Position {$i}",
                startDate: '2020-01-01',
                endDate: '2023-12-31',
            ));
        }

        // Add many skills
        for ($i = 0; $i < 100; $i++) {
            $resume->addSkill(new Skill(
                name: "Skill {$i}",
                level: SkillLevel::Advanced,
                keywords: ["keyword{$i}a", "keyword{$i}b"],
            ));
        }

        $builtResume = $resume->build();
        $json = json_encode($builtResume);

        // Build a second resume with the same structure
        $secondResume = new ResumeBuilder()
            ->basics(new Basics(
                name: 'John Doe',
                label: 'Software Engineer',
                email: 'john@example.com',
            ));

        // Add the same number of work experiences
        for ($i = 0; $i < 50; $i++) {
            $secondResume->addWork(new Work(
                name: "Company {$i}",
                position: "Position {$i}",
                startDate: '2020-01-01',
                endDate: '2023-12-31',
            ));
        }

        // Add the same number of skills
        for ($i = 0; $i < 100; $i++) {
            $secondResume->addSkill(new Skill(
                name: "Skill {$i}",
                level: SkillLevel::Advanced,
                keywords: ["keyword{$i}a", "keyword{$i}b"],
            ));
        }

        $secondBuiltResume = $secondResume->build();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Should complete within reasonable time (adjust threshold as needed)
        $this->assertLessThan(1.0, $executionTime, 'Resume processing took too long');
        $this->assertCount(50, $builtResume->work);
        $this->assertCount(100, $builtResume->skills);
    }

    private function buildModifiedResume(): Resume
    {
        $basics = new Basics(
            name: 'John Smith', // Changed name
            label: 'Software Engineer',
            email: 'john@example.com',
            phone: '+1-555-987-6543', // Added phone
        );

        return (new ResumeBuilder())
            ->basics($basics)
            ->addWork(new Work(
                name: 'Tech Corp',
                position: 'Senior Developer',
                startDate: '2020-01-01',
                endDate: '2023-12-31',
            ))
            ->build();
    }

    private function assertResumesAreEqual(Resume $expected, Resume $actual): void
    {
        // Compare basic properties
        $this->assertSame($expected->schema, $actual->schema);

        // Compare JSON representations for deep equality
        $expectedJson = json_encode($expected, JSON_THROW_ON_ERROR);
        $actualJson = json_encode($actual, JSON_THROW_ON_ERROR);

        $this->assertJsonStringEqualsJsonString($expectedJson, $actualJson);
    }
}
