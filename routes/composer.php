<?php

use App\Http\Controllers\Composer\ComposerPackageController;
use App\Http\Controllers\Composer\ComposerPackageVersionController;
use App\Http\Controllers\Composer\ComposerPackageVersionDownloadController;
use App\Http\Middleware\AuthenticateLicenseComposer;
use Illuminate\Support\Facades\Route;

Route::get('/packages.json', [ComposerPackageController::class, 'index'])
    ->name('packages');
Route::get('/composerPackage/{composerPackage:name}{dev?}', [ComposerPackageVersionController::class, 'index'])
    ->name('composerPackage.show')
    ->where('composerPackage', '([^\/]*\/.[^\/~]*)')
    ->where('dev', '(~dev)');

Route::webhooks('composer-package-update', 'composer-package-update');

Route::get('/composerPackage/{composerPackage:name}/{composerPackageVersion:version_code}/download', ComposerPackageVersionDownloadController::class)
    ->name('composerPackage.composerPackageVersion.download')
    ->where('composerPackage', '([^\/]*\/.[^\/~]*)')
    ->middleware('guard:license','auth.basic:license,username', 'can:download,composerPackage');