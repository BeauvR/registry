<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum ComposerPackageVersionType: string
{
    case DEV = 'dev';
    case STABLE = 'stable';

    public static function fromRoute(?string $routeParam): ComposerPackageVersionType
    {
        if (Str::contains($routeParam, 'dev')) {
            return self::DEV;
        }

        return self::STABLE;
    }
}
