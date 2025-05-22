<?php

namespace App\Http\Controllers\Npm;

use App\Enums\NpmPackageVersionType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Npm\NpmPackageVersionCollection;
use App\Models\NpmPackage;

class NpmPackageVersionController extends Controller
{
    public function index(NpmPackage $npmPackage, $dev = null): NpmPackageVersionCollection
    {
        $packageVersions = $npmPackage
            ->npmPackageVersions()
            ->with('npmPackage')
            ->where('version_type', '=', NpmPackageVersionType::fromRoute($dev))
            ->orderBy('version_code', 'desc')
            ->get();

        return NpmPackageVersionCollection::make($packageVersions);
    }
}
