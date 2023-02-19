<?php

namespace App\Policies;

use App\Models\ComposerPackage;
use App\Models\License;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ComposerPackagePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function download(License $license, ComposerPackage $composerPackages): Response
    {
        return $license->composerPackages()
            ->where('composer_packages.id', $composerPackages->id)
            ->exists()
            ? Response::allow()
            : Response::deny('You are not allowed to download this package');
    }
}
