<?php

namespace App\Http\Resources\Npm;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NpmPackageVersionCollection extends ResourceCollection
{
    public $collection;

    public static $wrap = null;

    public function toArray(Request $request): array
    {
        $packageName = $this->collection->first()?->npmPackage?->name;

        return [
            'packages' => [
                $packageName => NpmPackageVersionResource::collection($this->collection),
            ],
        ];
    }
}
