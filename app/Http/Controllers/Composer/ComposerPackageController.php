<?php

namespace App\Http\Controllers\Composer;

use App\Enums\ComposerVersionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Composer\StoreComposerPackagesRequest;
use App\Http\Requests\Composer\UpdateComposerPackagesRequest;
use App\Http\Resources\Composer\ComposerPackageCollection;
use App\Http\Resources\Composer\ComposerPackageVersionResource;
use App\Models\ComposerPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class ComposerPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): ComposerPackageCollection
    {
        return ComposerPackageCollection::make(ComposerPackage::query()->get());
    }
}
