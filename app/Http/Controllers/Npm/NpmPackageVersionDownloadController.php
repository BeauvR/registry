<?php

namespace App\Http\Controllers\Npm;

use App\Http\Controllers\Controller;
use App\Models\NpmPackage;
use App\Models\NpmPackageVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NpmPackageVersionDownloadController extends Controller
{
    public function __invoke(
        NpmPackage $npmPackage,
        NpmPackageVersion $npmPackageVersion,
    ): StreamedResponse|JsonResponse|Response {
        if (!Storage::exists($npmPackageVersion->storage_path)) {
            return response()->json([
                'error' => 'Package version dist not found',
            ], 404);
        }

        $file_name = Str::afterLast($npmPackageVersion->storage_path, '/');

        $headers = [
            'Content-Type' => Storage::mimeType($npmPackageVersion->storage_path),
            'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
        ];

        return \Response::make(Storage::get($npmPackageVersion->storage_path), 200, $headers);
    }
}
