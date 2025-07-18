<?php

declare(strict_types=1);

namespace JustSteveKing\Resume\Enums;

enum EducationLevel: string
{
    case Primary = 'Primary';
    case Secondary = 'Secondary';
    case HighSchool = 'High School';
    case Associate = 'Associate';
    case Bachelor = 'Bachelor';
    case Master = 'Master';
    case Doctorate = 'Doctorate';
    case Bootcamp = 'Bootcamp';
    case Other = 'Other';
}
