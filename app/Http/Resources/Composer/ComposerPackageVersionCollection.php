<?php

namespace App\Http\Resources\Composer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Storage;

class ComposerPackageVersionCollection extends ResourceCollection
{
    /**
     * The mapped collection instance.
     *
     * @var \Illuminate\Support\Collection<\App\Models\ComposerPackageVersion>
     */
    public $collection;

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $packageName = $this->collection->first()?->composerPackage?->name;

        return [
            'packages' => [
                $packageName => ComposerPackageVersionResource::collection($this->collection),
            ],
        ];
    }
}
