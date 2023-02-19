<?php

namespace App\Http\Resources\Composer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Storage;

class ComposerPackageCollection extends ResourceCollection
{
    /**
     * The mapped collection instance.
     *
     * @var \Illuminate\Support\Collection<\App\Models\ComposerPackage>
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
        return [
            'packages' => [],
            'metadata-url' => config('app.url') . route('composer.composerPackage.show', ['%package%'], false),
            'available-packages' => $this->collection->pluck('name')
        ];
    }
}
