<h1 align="center">
    Private registry (composer)
</h1>

<p align="center">
    <a href="https://github.com/BeauvR/registry/blob/main/LICENSE.md" target="blank">
        <img src="https://img.shields.io/github/license/BeauvR/registry" alt="GitHub">
    </a>
    <a href="https://github.com/BeauvR/registry/pulls" target="blank">
        <img src="https://img.shields.io/github/issues-pr/BeauvR/registry" alt="GitHub pull requests">
    </a>
    <a href="https://github.com/BeauvR/registry/issues" target="blank">
        <img src="https://img.shields.io/github/issues/BeauvR/registry" alt="GitHub issues">
    </a>
</p>

<hr>

<strong>Free and open source.</strong>

> A private registry for composer with licensing support. Written in PHP using [Laravel Framework](https://laravel.com/).

See [#contributing](#Contributing) for more details on how you can help improving this project. We're always down to improve and receive feedback.

## Features
* A private registry for composer.
* Licensing support.
* Easy to use.
* Easy to install.

## License
Please refer to [LICENSE.md](https://github.com/BeauvR/registry/blob/main/LICENSE.md) for this project's license.

## Contributors
This list only contains some of the most notable contributors. For the full list, refer to [GitHub's contributors graph](https://github.com/BeauvR/registry/graphs/contributors).
* [BeauvR](https://github.com/BeauvR) (Beau) - creator and maintainer .

## Setup
### Prerequisites
* PHP 8.2+.
* Composer.
* SQL (database).
* A webserver (Apache, Nginx, etc).
* A domain name.
* A SSL certificate.
* A storage server (S3, etc).
* PHP zip extension.
* Git installed.

### Installation
Grab yourself a copy of this repository:
```bash
$ git clone
```

Install all the required dependencies (we use composer):
```bash
$ composer install
```

Create a new file called ``.env`` and copy the contents from ``.env.example`` over to it, then apply your configurations.
```bash
$ cp .env.example .env
```

Create a private and unique application key:
```bash
$ php artisan key:generate
```

Run database migrations so that we can store things:
```bash
$ php artisan migrate
```

### Note
The docker configuration in this project is not production ready. It is only used for development.

## Usage

### Adding a composer package
To add a package, you can use the following command:
```bash
$ php artisan create:composer-package
```

### Adding a composer package version
To create a new version of a package, you need to call a webhook. This webhook is located at ``https://yourdomain.com/composer/composer-package-update``. This webhook must be singed as described here and accepts a POST request with the following parameters:
* ``version_code``: The version code of the new release.
* ``version_type``: The version type of the new release. This can be ``stable``, ``dev``.
* ``source_reference``: The source reference of the new release. This must be a valid git reference. This can be a branch, tag or commit hash.
* ``composer_package_id``: The ID of the composer package that you want to add a new version to.
After calling this webhook with the correct parameters, there will be a job queued that will create a new version of the package. This job will be processed by the queue worker.

### Issuing a license
To issue a license, you can use the following command:
```bash
$ php artisan create:license
```

### Linking a license to a composer package
To link a license to a composer package, you can use the following command:
```bash
$ php artisan link:license-to-composer-package
```

### Adding the composer repository to your composer.json of the project where you want to use the package
```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://yourdomain.com/composer"
        }
    ]
}
```

### Signing the webhook
To sign the webhook, you need to set the ``COMPOSER_WEBHOOK_SECRET`` environment variable to a random string. This string will be used to sign the webhook. The webhook will be signed using the ``HMAC-SHA256`` algorithm. The signature should be created for the whole body of the request and should be sent in the `signature` header. You can create a signature with the following code:
```PHP
$computedSignature = hash_hmac('sha256', $request->getContent(), $configuredSigningSecret);
```

## Contributing
This section describes how you can help contribute. We're always down to improve and receive feedback.
First off, thank you for considering contributing to this project. It's people like you that make this project possible.

### Setup 
Follow the [setup](#Setup) instructions to get started.

### Development
We use Laravel Sail for development. You can use it to run the project locally. 
```bash
$ ./vendor/bin/sail up
```

### Testing
We use PHPUnit for testing. You can run the tests using the following command:
```bash
$ ./vendor/bin/sail test
```

## Planned features
* Web interface.
* NPM registry.
