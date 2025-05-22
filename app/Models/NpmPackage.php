<?php

namespace App\Models;

use App\Enums\NpmPackageVersionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NpmPackage extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'git_source',
    ];

    public function npmPackageVersions(): HasMany
    {
        return $this->hasMany(NpmPackageVersion::class)
            ->where('status', '=', NpmPackageVersionStatus::READY);
    }

    public function licenses(): BelongsToMany
    {
        return $this->belongsToMany(License::class)->using(NpmPackageLicense::class);
    }
}
