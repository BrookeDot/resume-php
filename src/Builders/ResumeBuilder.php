<?php

declare(strict_types=1);

namespace JustSteveKing\Resume\Builders;

use JustSteveKing\Resume\DataObjects\Award;
use JustSteveKing\Resume\DataObjects\Basics;
use JustSteveKing\Resume\DataObjects\Certificate;
use JustSteveKing\Resume\DataObjects\Education;
use JustSteveKing\Resume\DataObjects\Interest;
use JustSteveKing\Resume\DataObjects\Language;
use JustSteveKing\Resume\DataObjects\Project;
use JustSteveKing\Resume\DataObjects\Publication;
use JustSteveKing\Resume\DataObjects\Reference;
use JustSteveKing\Resume\DataObjects\Resume;
use JustSteveKing\Resume\DataObjects\Skill;
use JustSteveKing\Resume\DataObjects\Volunteer;
use JustSteveKing\Resume\DataObjects\Work;
use JustSteveKing\Resume\Enums\ResumeSchema;
use LogicException;

final class ResumeBuilder
{
    private ?Basics $basics = null;
    /** @var list<Work> $work */
    private array $work = [];
    /** @var list<Volunteer> $volunteer */
    private array $volunteer = [];
    /** @var list<Education> $education */
    private array $education = [];
    /** @var list<Award> $awards */
    private array $awards = [];
    /** @var list<Certificate> $certificates */
    private array $certificates = [];
    /** @var list<Publication> $publications */
    private array $publications = [];
    /** @var list<Skill> $skills */
    private array $skills = [];
    /** @var list<Language> $languages */
    private array $languages = [];
    /** @var list<Interest> $interests */
    private array $interests = [];
    /** @var list<Reference> $references */
    private array $references = [];
    /** @var list<Project> $projects */
    private array $projects = [];

    /**
     * Add basics to the résumé.
     *
     * @param Basics $basics
     * @return ResumeBuilder
     */
    public function basics(Basics $basics): ResumeBuilder
    {
        $this->basics = $basics;
        return $this;
    }

    /**
     * Add a work experience to the résumé.
     *
     * @param Work $work
     * @return ResumeBuilder
     */
    public function addWork(Work $work): ResumeBuilder
    {
        $this->work[] = $work;
        return $this;
    }

    /**
     * Add a volunteer experience to the résumé.
     *
     * @param Volunteer $volunteer
     * @return ResumeBuilder
     */
    public function addVolunteer(Volunteer $volunteer): ResumeBuilder
    {
        $this->volunteer[] = $volunteer;
        return $this;
    }

    /**
     * Add an education entry to the résumé.
     *
     * @param Education $education
     * @return ResumeBuilder
     */
    public function addEducation(Education $education): ResumeBuilder
    {
        $this->education[] = $education;
        return $this;
    }

    /**
     * Add an award to the résumé.
     *
     * @param Award $award
     * @return ResumeBuilder
     */
    public function addAward(Award $award): ResumeBuilder
    {
        $this->awards[] = $award;
        return $this;
    }

    /**
     * Add a certificate to the résumé.
     *
     * @param Certificate $certificate
     * @return ResumeBuilder
     */
    public function addCertificate(Certificate $certificate): ResumeBuilder
    {
        $this->certificates[] = $certificate;
        return $this;
    }

    /**
     * Add a publication to the résumé.
     *
     * @param Publication $publication
     * @return ResumeBuilder
     */
    public function addPublication(Publication $publication): ResumeBuilder
    {
        $this->publications[] = $publication;
        return $this;
    }

    /**
     * Add a skill to the résumé.
     *
     * @param Skill $skill
     * @return ResumeBuilder
     */
    public function addSkill(Skill $skill): ResumeBuilder
    {
        $this->skills[] = $skill;
        return $this;
    }

    /**
     * Add a language to the résumé.
     *
     * @param Language $language
     * @return ResumeBuilder
     */
    public function addLanguage(Language $language): ResumeBuilder
    {
        $this->languages[] = $language;
        return $this;
    }

    /**
     * Add an interest to the résumé.
     *
     * @param Interest $interest
     * @return ResumeBuilder
     */
    public function addInterest(Interest $interest): ResumeBuilder
    {
        $this->interests[] = $interest;
        return $this;
    }

    /**
     * Add a reference to the résumé.
     *
     * @param Reference $reference
     * @return ResumeBuilder
     */
    public function addReference(Reference $reference): ResumeBuilder
    {
        $this->references[] = $reference;
        return $this;
    }

    /**
     * Add a project to the résumé.
     *
     * @param Project $project
     * @return ResumeBuilder
     */
    public function addProject(Project $project): ResumeBuilder
    {
        $this->projects[] = $project;
        return $this;
    }

    /**
     * Build the résumé.
     *
     * @return Resume
     * @throws LogicException
     */
    public function build(): Resume
    {
        if ( ! $this->basics) {
            throw new LogicException(
                message: 'Basics section is required',
            );
        }

        return new Resume(
            basics: $this->basics,
            work: $this->work,
            volunteer: $this->volunteer,
            education: $this->education,
            awards: $this->awards,
            certificates: $this->certificates,
            publications: $this->publications,
            skills: $this->skills,
            languages: $this->languages,
            interests: $this->interests,
            references: $this->references,
            projects: $this->projects,
            schema: ResumeSchema::V1,
        );
    }
}
