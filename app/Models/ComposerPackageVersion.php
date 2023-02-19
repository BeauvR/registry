<?php

namespace App\Models;

use App\Enums\ComposerPackageVersionStatus;
use App\Enums\ComposerPackageVersionType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ComposerPackageVersion extends Model
{
    use HasFactory;

    public $fillable = [
        'composer_package_id',
        'status',
        'version_code',
        'version_type',
        'source_reference',
        'storage_path',
        'storage_shasum',
        'composer_json_content',
    ];

    public $casts = [
        'status' => ComposerPackageVersionStatus::class,
        'version_type' => ComposerPackageVersionType::class,
        'composer_json_content' => 'array',
    ];

    public function composerPackage(): BelongsTo
    {
        return $this->belongsTo(ComposerPackage::class);
    }

    public function normalizedVersion(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->version_type === ComposerPackageVersionType::STABLE
                ? Str::of($this->version_code)
                    ->replaceMatches('/^v/', '')
                    ->explode('.')
                    ->pad(4, '0')
                    ->implode('.')
                : $this->version_code,
        );
    }
}
