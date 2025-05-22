<?php

namespace App\Models;

use App\Enums\NpmPackageVersionStatus;
use App\Enums\NpmPackageVersionType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class NpmPackageVersion extends Model
{
    use HasFactory;

    public $fillable = [
        'npm_package_id',
        'status',
        'version_code',
        'version_type',
        'source_reference',
        'storage_path',
        'storage_shasum',
        'package_json_content',
    ];

    public $casts = [
        'status' => NpmPackageVersionStatus::class,
        'version_type' => NpmPackageVersionType::class,
        'package_json_content' => 'array',
    ];

    public function npmPackage(): BelongsTo
    {
        return $this->belongsTo(NpmPackage::class);
    }

    public function normalizedVersion(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->version_type === NpmPackageVersionType::STABLE
                ? Str::of($this->version_code)
                    ->replaceMatches('/^v/', '')
                    ->explode('.')
                    ->pad(4, '0')
                    ->implode('.')
                : $this->version_code,
        );
    }
}
