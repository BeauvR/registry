<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ComposerPackage extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'git_source',
    ];

    public function composerPackageVersions(): HasMany
    {
        return $this->hasMany(ComposerPackageVersion::class);
    }

    public function licenses(): BelongsToMany
    {
        return $this->belongsToMany(License::class)->using(ComposerPackageLicense::class);
    }
}
