<?php

namespace App\Http\Resources\Npm;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NpmPackageCollection extends ResourceCollection
{
    public $collection;

    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'packages' => [],
            'metadata-url' => config('app.url') . route('npm.npmPackage.show', ['%package%'], false),
            'available-packages' => $this->collection->pluck('name'),
        ];
    }
}
