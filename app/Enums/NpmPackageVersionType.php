<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum NpmPackageVersionType: string
{
    case DEV = 'dev';
    case STABLE = 'stable';

    public static function fromRoute(?string $routeParam): NpmPackageVersionType
    {
        if (Str::contains($routeParam, 'dev')) {
            return self::DEV;
        }

        return self::STABLE;
    }
}
