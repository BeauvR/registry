<?php

namespace App\Http\Controllers\Npm;

use App\Http\Controllers\Controller;
use App\Http\Resources\Npm\NpmPackageCollection;
use App\Models\NpmPackage;

class NpmPackageController extends Controller
{
    public function index(): NpmPackageCollection
    {
        return NpmPackageCollection::make(NpmPackage::query()->get());
    }
}
