<?php

use App\Http\Controllers\Npm\NpmPackageController;
use App\Http\Controllers\Npm\NpmPackageVersionController;
use App\Http\Controllers\Npm\NpmPackageVersionDownloadController;
use Illuminate\Support\Facades\Route;

Route::get('/packages.json', [NpmPackageController::class, 'index'])
    ->name('packages');

Route::get('/npmPackage/{npmPackage:name}{dev?}', [NpmPackageVersionController::class, 'index'])
    ->name('npmPackage.show')
    ->where('npmPackage', '([^\/]*\/.[^\/~]*)')
    ->where('dev', '(~dev)');

Route::get('/npmPackage/{npmPackage:name}/{npmPackageVersion:version_code}/download', NpmPackageVersionDownloadController::class)
    ->name('npmPackage.npmPackageVersion.download')
    ->where('npmPackage', '([^\/]*\/.[^\/~]*)')
    ->middleware('guard:license','auth.basic:license,username', 'can:download,npmPackage');
