<?php

namespace App\Policies;

use App\Models\NpmPackage;
use App\Models\License;
use Illuminate\Auth\Access\Response;

class NpmPackagePolicy
{
    public function download(License $license, NpmPackage $npmPackage): Response
    {
        return $license->npmPackages()
            ->where('npm_packages.id', $npmPackage->id)
            ->exists()
            ? Response::allow()
            : Response::deny('You are not allowed to download this package');
    }
}
