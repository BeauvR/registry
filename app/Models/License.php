<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\ComposerPackage;
use App\Models\ComposerPackageLicense;
use App\Models\NpmPackage;
use App\Models\NpmPackageLicense;

class License extends Authenticatable
{
    use HasFactory;

    public $fillable = [
        'username',
        'password',
    ];

    public function composerPackages(): BelongsToMany
    {
        return $this->belongsToMany(ComposerPackage::class)->using(ComposerPackageLicense::class);
    }

    public function npmPackages(): BelongsToMany
    {
        return $this->belongsToMany(NpmPackage::class)->using(NpmPackageLicense::class);
    }
}
