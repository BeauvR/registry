<?php

namespace App\Webhook;

use App\Enums\ComposerPackageVersionType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Spatie\WebhookClient\WebhookProfile\WebhookProfile;

class ComposerPackageWebhookProfile implements WebhookProfile
{
    public function shouldProcess(Request $request): bool
    {
        $request->validate(self::rules());

        return true;
    }

    public static function rules(): array
    {
        return [
            'version_code' => ['required'],
            'version_type' => ['required', new Enum(ComposerPackageVersionType::class)],
            'source_reference' => ['required', 'max:40'],
            'composer_package_id' => ['required', 'exists:composer_packages,id']
        ];
    }
}