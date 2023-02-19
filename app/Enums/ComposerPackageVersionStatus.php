<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum ComposerPackageVersionStatus: string
{
    case DRAFT = 'draft';
    case READY = 'ready';
}
