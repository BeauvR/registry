<?php

namespace App\Enums;

enum NpmPackageVersionStatus: string
{
    case DRAFT = 'draft';
    case READY = 'ready';
}
