<?php

namespace Tests\Unit\Jobs;

use App\Enums\ComposerPackageVersionStatus;
use App\Enums\ComposerPackageVersionType;
use App\Http\Middleware\SetAuthGuard;
use App\Jobs\ProcessComposerPackageVersionWebhook;
use App\Models\ComposerPackage;
use App\Models\ComposerPackageVersion;
use Closure;
use ErrorException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Process\Exceptions\ProcessFailedException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use JsonException;
use Mockery;
use RuntimeException;
use Spatie\WebhookClient\Models\WebhookCall;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use ZipArchive;

class ProcessComposerVersionWebhookTest extends TestCase
{
    use WithFaker;

    private ComposerPackage $composerPackage;
    private WebhookCall $webhookCall;
    private ProcessComposerPackageVersionWebhook $job;

    public function setUp(): void
    {
        parent::setUp();

        Process::preventStrayProcesses();

        $this->composerPackage = ComposerPackage::factory()->create();
        $this->webhookCall = new WebhookCall([
            'payload' => [
                'composer_package_id' => $this->composerPackage->id,
                'version_code' => '1.1.1',
                'version_type' => ComposerPackageVersionType::STABLE,
                'source_reference' => $this->faker->sha1,
            ],
        ]);
        $this->job = new ProcessComposerPackageVersionWebhook($this->webhookCall);
    }

    public function test_the_create_draft_version_method_creates_a_new_composer_package_version_model(): void
    {
        $this->job->createDraftVersion();

        $this->assertDatabaseHas('composer_package_versions', [
            'composer_package_id' => $this->composerPackage->id,
            'version_code' => '1.1.1',
            'version_type' => ComposerPackageVersionType::STABLE,
        ]);
    }

    public function test_the_prepare_working_dir_method_creates_a_temporary_directory(): void
    {
        $this->job->createDraftVersion();

        Mockery::mock(Filesystem::class)
            ->shouldReceive('mkdir')
            ->once()
            ->withArgs(function ($path) {
                return 'tmp/composer/' . $this->job->composerPackageVersion->id === $path;
            })
            ->andReturnNull();

        $tmpDir = $this->job->prepareWorkingDir();

        $this->assertEquals(storage_path('tmp/composer/' . $this->job->composerPackageVersion->id), $tmpDir);
    }

    public function test_the_ensure_git_is_installed_method_throws_an_exception_if_git_is_not_installed(): void
    {
        Process::fake([
            "'git' '--version'" => 'bash: command not found: git',
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Git is not installed');

        $this->job->ensureGitIsInstalled();
    }

    public function test_the_ensure_git_is_installed_method_does_not_throw_an_exception_if_git_is_installed(): void
    {
        $gitVersionCommand = [
            'git',
            '--version',
        ];
        Process::fake([
            "'" . implode("' '", $gitVersionCommand) . "'" => 'git version 2.30.1',
        ]);

        $this->job->ensureGitIsInstalled();

        Process::assertRanTimes(static fn ($process) => $process->command === $gitVersionCommand);
    }

    public function test_the_clone_repository_method_throws_an_exception_if_the_clone_command_fails(): void
    {
        $this->job->createDraftVersion();
        $gitUrl = $this->composerPackage->git_source;
        $cloneCommand = [
            'git',
            'clone',
            '--no-checkout',
            $gitUrl,
            'package',
        ];

        Process::fake([
            "'" . implode("' '", $cloneCommand) . "'" => Process::describe()->exitCode(1),
        ]);

        $this->expectException(ProcessFailedException::class);
        $this->expectExceptionMessage("The process \"'" . implode("' '", $cloneCommand) . "'\" failed.");

        $this->job->cloneRepository($this->faker->filePath());
    }

    public function test_the_clone_repository_method_throws_an_exception_if_the_checkout_command_fails(): void
    {
        $this->job->createDraftVersion();
        $gitUrl = $this->composerPackage->git_source;
        $sourceReference = $this->webhookCall->payload['source_reference'];
        $cloneCommand = [
            'git',
            'clone',
            '--no-checkout',
            $gitUrl,
            'package',
        ];
        $checkoutCommand = [
            'git',
            'checkout',
            $sourceReference,
        ];

        Process::fake([
            "'" . implode("' '", $cloneCommand) . "'" => 'Cloning...',
            "'" . implode("' '", $checkoutCommand) . "'" => Process::describe()->exitCode(1),
        ]);

        $this->expectException(ProcessFailedException::class);
        $this->expectExceptionMessage("The process \"'" . implode("' '", $checkoutCommand) . "'\" failed.");

        $this->job->cloneRepository($this->faker->filePath());
    }

    public function test_the_clone_repository_method_clones_the_repository_correctly(): void
    {
        $this->job->createDraftVersion();
        $gitUrl = $this->composerPackage->git_source;
        $sourceReference = $this->webhookCall->payload['source_reference'];
        $cloneCommand = [
            'git',
            'clone',
            '--no-checkout',
            $gitUrl,
            'package',
        ];
        $checkoutCommand = [
            'git',
            'checkout',
            $sourceReference,
        ];

        Process::fake([
            "'" . implode("' '", $cloneCommand) . "'" => 'Cloning...',
            "'" . implode("' '", $checkoutCommand) . "'" => 'Checking out...',
        ]);

        $this->job->cloneRepository($this->faker->filePath());

        Process::assertRanTimes(static fn ($process) => $process->command === $cloneCommand);
        Process::assertRanTimes(static fn ($process) => $process->command === $checkoutCommand);
    }

    /**
     * @throws \JsonException
     */
    public function test_the_update_composer_json_content_method_will_throw_a_exception_when_the_file_is_not_found(
    ): void {
        $this->job->createDraftVersion();

        $workingDir = storage_path('tmp/composer');

        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage("File does not exist at path ${workingDir}/package/composer.json");

        $this->job->updateComposerJsonContent($workingDir);
    }

    /**
     * @throws \JsonException
     */
    public function test_the_update_composer_json_content_method_will_throw_a_exception_when_the_file_is_not_valid_json(): void
    {
        $this->job->createDraftVersion();

        $workingDir = storage_path('tmp/composer');

        File::partialMock()
            ->shouldReceive('get')
            ->once()
            ->withArgs(function ($path) use ($workingDir) {
                return "${workingDir}/package/composer.json" === $path;
            })
            ->andReturn('invalid json');

        $this->expectException(JsonException::class);
        $this->expectExceptionMessage("Syntax error");

        $this->job->updateComposerJsonContent($workingDir);
    }

    /**
     * @throws \JsonException
     */
    public function test_the_update_composer_json_content_method_will_update_the_composer_json_content_in_the_db(): void
    {
        $this->job->createDraftVersion();

        $workingDir = storage_path('tmp/composer');

        File::partialMock()
            ->shouldReceive('get')
            ->once()
            ->withArgs(function ($path) use ($workingDir) {
                return "${workingDir}/package/composer.json" === $path;
            })
            ->andReturn('{"name": "test"}');

        $this->job->updateComposerJsonContent($workingDir);
        $this->job->composerPackageVersion->save();

        $this->job->composerPackageVersion->refresh();

        $this->assertEquals(['name' => 'test'], $this->job->composerPackageVersion->composer_json_content);
    }

    public function test_the_upload_package_method_will_upload_correctly(): void
    {
        $this->job->createDraftVersion();

        $workingDir = storage_path('tmp/composer');

        File::partialMock()
            ->shouldReceive('get')
            ->once()
            ->withArgs(function ($path) use ($workingDir) {
                return "${workingDir}/package.zip" === $path;
            })
            ->andReturn('content');

        Storage::fake('composer-packages');

        $this->job->uploadPackage($workingDir);
        $this->job->composerPackageVersion->save();

        $this->job->composerPackageVersion->refresh();
        $this->assertNotNull($this->job->composerPackageVersion->storage_path);

        Storage::assertExists($this->job->composerPackageVersion->storage_path);
    }

    public function test_the_calculate_shasum_method_will_calculate_correctly(): void
    {
        $this->job->createDraftVersion();

        $workingDir = storage_path('tmp/composer');

        File::partialMock()
            ->shouldReceive('hash')
            ->once()
            ->withArgs(function ($path, $algorithm) use ($workingDir) {
                return $path === "${workingDir}/package.zip" && $algorithm === 'sha1';
            })
            ->andReturn('hash');

        $this->job->calculateShasum($workingDir);
        $this->job->composerPackageVersion->save();

        $this->job->composerPackageVersion->refresh();
        $this->assertEquals('hash', $this->job->composerPackageVersion->storage_shasum);
    }

    public function test_the_delete_working_dir_method_will_delete_the_working_dir(): void
    {
        $this->job->createDraftVersion();

        $workingDir = storage_path('tmp/composer');

        File::partialMock()
            ->shouldReceive('deleteDirectory')
            ->once()
            ->withArgs(function ($path) use ($workingDir) {
                return $path === $workingDir;
            });

        $this->job->deleteWorkingDir($workingDir);
    }

    public function test_the_mark_version_as_ready_method_will_mark_the_version_as_ready(): void
    {
        $this->job->createDraftVersion();

        $this->job->markVersionReady();

        $this->job->composerPackageVersion->refresh();
        $this->assertEquals(ComposerPackageVersionStatus::READY, $this->job->composerPackageVersion->status);
    }
}
