<?php

namespace App\Http\Controllers\Composer;

use App\Enums\ComposerPackageVersionType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Composer\ComposerPackageVersionCollection;
use App\Models\ComposerPackage;

class ComposerPackageVersionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ComposerPackage $composerPackage, $dev = null): ComposerPackageVersionCollection
    {
        $packageVersions = $composerPackage
            ->composerPackageVersions()
            ->with('composerPackage')
            ->where('version_type', '=', ComposerPackageVersionType::fromRoute($dev))
            ->orderBy('version_code', 'desc')
            ->get();

        return ComposerPackageVersionCollection::make($packageVersions);
    }
}
