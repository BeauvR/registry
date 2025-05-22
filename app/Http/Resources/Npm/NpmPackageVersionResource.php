<?php

namespace App\Http\Resources\Npm;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\NpmPackageVersion
 */
class NpmPackageVersionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->npmPackage->name,
            'version' => $this->version_code,
            'version_normalized' => $this->normalized_version,
            'source' => [
                'type' => 'git',
                'url' => $this->npmPackage->git_source,
                'reference' => $this->source_reference,
            ],
            'dist' => [
                'type' => 'tar',
                'url' => config('app.url') . route(
                    'npm.npmPackage.npmPackageVersion.download',
                    [$this->npmPackage, $this],
                    false,
                ),
                'reference' => $this->source_reference,
                'shasum' => $this->storage_shasum,
            ],
            'time' => $this->created_at->toIso8601String(),
            ...$this->package_json_content,
        ];
    }
}
