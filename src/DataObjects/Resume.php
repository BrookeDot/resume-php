<?php

declare(strict_types=1);

namespace JustSteveKing\Resume\DataObjects;

use JsonSerializable;
use JustSteveKing\Resume\Attributes\Field;
use JustSteveKing\Resume\Enums\Network;
use JustSteveKing\Resume\Enums\ResumeSchema;

final readonly class Resume implements JsonSerializable
{
    /**
     * @param ResumeSchema $schema
     * @param Basics $basics
     * @param array<Work> $work
     * @param array<Volunteer> $volunteer
     * @param array<Education> $education
     * @param array<Award> $awards
     * @param array<Certificate> $certificates
     * @param array<Publication> $publications
     * @param array<Skill> $skills
     * @param array<Language> $languages
     * @param array<Interest> $interests
     * @param array<Reference> $references
     * @param array<Project> $projects
     */
    public function __construct(
        #[Field('basics')]
        public Basics       $basics,
        #[Field('work')]
        public array        $work = [],
        #[Field('volunteer')]
        public array        $volunteer = [],
        #[Field('education')]
        public array        $education = [],
        #[Field('awards')]
        public array        $awards = [],
        #[Field('certificates')]
        public array        $certificates = [],
        #[Field('publications')]
        public array        $publications = [],
        #[Field('skills')]
        public array        $skills = [],
        #[Field('languages')]
        public array        $languages = [],
        #[Field('interests')]
        public array        $interests = [],
        #[Field('references')]
        public array        $references = [],
        #[Field('projects')]
        public array        $projects = [],
        #[Field('$schema')]
        public ResumeSchema $schema = ResumeSchema::V1,
    ) {}

    /**
     * @return non-empty-array<'$schema'|'basics'|'work'|'volunteer'|'education'|'awards'|'certificates'|'publications'|'skills'|'languages'|'interests'|'references'|'projects', mixed>
     */
    public function jsonSerialize(): array
    {
        $data = [
            '$schema' => $this->schema->value,
            'basics' => $this->basics->jsonSerialize(),
        ];

        // Only include non-empty arrays
        $arrayFields = [
            'work' => $this->work,
            'volunteer' => $this->volunteer,
            'education' => $this->education,
            'awards' => $this->awards,
            'certificates' => $this->certificates,
            'publications' => $this->publications,
            'skills' => $this->skills,
            'languages' => $this->languages,
            'interests' => $this->interests,
            'references' => $this->references,
            'projects' => $this->projects,
        ];

        foreach ($arrayFields as $key => $items) {
            if ( ! empty($items)) {
                $data[$key] = array_map(
                    static fn($item): array => $item->jsonSerialize(),
                    $items,
                );
            }
        }

        return $data;
    }

    /**
     * Get a summary of the resume content.
     *
     * @return array<string, int|bool|string|null>
     */
    public function getSummary(): array
    {
        return [
            'name' => $this->basics->name,
            'email' => $this->basics->email,
            'work_experiences' => count($this->work),
            'education_entries' => count($this->education),
            'skills' => count($this->skills),
            'projects' => count($this->projects),
            'languages' => count($this->languages),
            'has_volunteer_experience' => ! empty($this->volunteer),
            'has_awards' => ! empty($this->awards),
            'has_publications' => ! empty($this->publications),
        ];
    }

    /**
     * Transform the résumé into a structured array for JSON-LD.
     *
     * @param Resume $resume
     * @return array<string, mixed>
     */
    public function toJsonLd(Resume $resume): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $resume->basics->name,
            'url' => $resume->basics->url,
            'jobTitle' => $resume->basics->label,
            'sameAs' => array_filter(array_map(
                static fn($profile) => $profile->url,
                array_filter($resume->basics->profiles, static fn($profile) => $profile->network instanceof Network),
            )),
            'knowsAbout' => array_map(
                static fn($skill) => $skill->name,
                $resume->skills
            ),
        ];
    }
}
