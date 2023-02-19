<?php

namespace App\Http\Resources\Composer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin \App\Models\ComposerPackageVersion
 */
class ComposerPackageVersionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "name" => $this->composerPackage->name,
            'version' => $this->version_code,
            'version_normalized' => $this->normalized_version,
            'source' => [
                'type' => 'git',
                'url' => $this->composerPackage->git_source,
                'reference' => $this->source_reference,
            ],
            'dist' => [
                'type' => 'zip',
                'url' => config('app.url') . route(
                    'composer.composerPackage.composerPackageVersion.download',
                    [$this->composerPackage, $this],
                    false,
                ),
                'reference' => $this->source_reference,
                'shasum' => $this->storage_shasum,
            ],
            'type' => 'library',
            'time' => $this->created_at->toIso8601String(),
            ...$this->composer_json_content,
        ];
    }
}
