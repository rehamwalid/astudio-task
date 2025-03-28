<?php

namespace App\Enums;

enum JobStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';
}