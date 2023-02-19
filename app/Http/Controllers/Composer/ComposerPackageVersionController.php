<?php

namespace App\Http\Controllers\Composer;

use App\Enums\ComposerPackageVersionType;
use App\Enums\ComposerVersionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Composer\StoreComposerPackagesRequest;
use App\Http\Requests\Composer\UpdateComposerPackagesRequest;
use App\Http\Resources\Composer\ComposerPackageVersionCollection;
use App\Http\Resources\Composer\ComposerPackageVersionResource;
use App\Models\ComposerPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

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
