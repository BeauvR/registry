<?php

namespace App\Jobs;

use App\Enums\ComposerPackageVersionStatus;
use App\Models\ComposerPackageVersion;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Finder\Finder;
use ZipArchive;
use Symfony\Component\Filesystem\Filesystem;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob as SpatieProcessWebhookJob;

class ProcessComposerPackageVersionWebhook extends SpatieProcessWebhookJob
{
    public ComposerPackageVersion $composerPackageVersion;

    /**
     * Execute the job.
     *
     * @throws \JsonException|\RuntimeException
     */
    public function handle(): void
    {
        $this->createDraftVersion();

        $tmpDir = $this->prepareWorkingDir();

        $this->ensureGitIsInstalled();

        $this->cloneRepository($tmpDir);

        $this->updateComposerJsonContent($tmpDir);

        $this->zipPackage($tmpDir);

        $this->uploadPackage($tmpDir);

        $this->calculateShasum($tmpDir);

        $this->markVersionReady();

        $this->deleteWorkingDir($tmpDir);
    }

    public function createDraftVersion(): void
    {
        $this->composerPackageVersion = ComposerPackageVersion::create([
            'composer_package_id' => $this->webhookCall->payload['composer_package_id'],
            'version_code' => $this->webhookCall->payload['version_code'],
            'version_type' => $this->webhookCall->payload['version_type'],
            'source_reference' => $this->webhookCall->payload['source_reference'],
            'status' => ComposerPackageVersionStatus::DRAFT,
        ]);
    }

    /**
     * @throws \RuntimeException
     */
    public function prepareWorkingDir(): string
    {
        $tmpDir = storage_path(
            'tmp/composer/' . $this->composerPackageVersion->id,
        );

        (new Filesystem())->mkdir($tmpDir);

        return $tmpDir;
    }

    /**
     * @throws \RuntimeException
     */
    public function ensureGitIsInstalled(): void
    {
        $gitVersion = Process::run(['git', '--version'])
            ->throw()
            ->output();

        if (!Str::of($gitVersion)->contains('git version')) {
            throw new RuntimeException('Git is not installed');
        }
    }

    /**
     * @throws \RuntimeException
     */
    public function cloneRepository(string $tmpDir): void
    {
        Process::path($tmpDir)
            ->run([
                'git',
                'clone',
                '--no-checkout',
                $this->composerPackageVersion->composerPackage->git_source,
                'package',
            ])
            ->throw();

        Process::path($tmpDir . '/package')
            ->run([
                'git',
                'checkout',
                $this->composerPackageVersion->source_reference,
            ])
            ->throw();
    }

    /**
     * @throws \JsonException
     */
    public function updateComposerJsonContent(string $tmpDir): void
    {
        $composerJson = File::get($tmpDir . '/package/composer.json');

        $this->composerPackageVersion->composer_json_content = json_decode(
            $composerJson, true, 512, JSON_THROW_ON_ERROR,
        );
    }

    public function zipPackage(string $tmpDir): void
    {
        $zip = new ZipArchive();
        $zip->open($tmpDir . '/package.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $files = (new Finder())->in($tmpDir . '/package')
            ->exclude(['.git', 'vendor'])
            ->files()
            ->ignoreVCS(true)
            ->ignoreDotFiles(true)
            ->ignoreUnreadableDirs();

        collect($files)
            ->each(fn (SplFileInfo $file) => $zip->addFile($file->getPathname(), $file->getRelativePathName()));

        $zip->close();
    }

    public function uploadPackage(string $tmpDir): void
    {
        $filePath = 'composer-packages/' .
            $this->composerPackageVersion->composerPackage->id . '/' .
            Str::uuid() .
            '.zip';
        Storage::put(
            $filePath,
            File::get($tmpDir . '/package.zip'),
        );

        $this->composerPackageVersion->storage_path = $filePath;
    }

    public function calculateShasum(string $tmpDir): void
    {
        $this->composerPackageVersion->storage_shasum = File::hash($tmpDir . '/package.zip', 'sha1');
    }

    public function deleteWorkingDir(string $tmpDir): void
    {
        File::deleteDirectory($tmpDir);
    }

    public function markVersionReady(): void
    {
        $this->composerPackageVersion->status = ComposerPackageVersionStatus::READY;
        $this->composerPackageVersion->save();
    }
}
