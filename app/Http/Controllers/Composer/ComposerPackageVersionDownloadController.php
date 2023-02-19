<?php

namespace App\Http\Controllers\Composer;

use App\Http\Controllers\Controller;
use App\Models\ComposerPackage;
use App\Models\ComposerPackageVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ComposerPackageVersionDownloadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __invoke(
        ComposerPackage $composerPackage,
        ComposerPackageVersion $composerPackageVersion,
    ): StreamedResponse|JsonResponse|Response {
        if (!Storage::exists($composerPackageVersion->storage_path)) {
            return response()->json([
                'error' => 'Package version dist not found',
            ], 404);
        }

        $file_name = Str::afterLast($composerPackageVersion->storage_path, '/');

        $headers = [
            'Content-Type' => Storage::mimeType($composerPackageVersion->storage_path),
            'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
        ];

        return \Response::make(Storage::get($composerPackageVersion->storage_path), 200, $headers);
    }
}
