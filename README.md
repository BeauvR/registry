<h1 align="center">
    Private registery (composer)
</h1>

<p align="center">
    <a href="https://github.com/BeauvR/registery/blob/master/LICENSE.md" target="blank">
        <img src="https://img.shields.io/github/license/EBeauvR/registery" alt="GitHub">
    </a>
    <a href="https://github.com/BeauvR/registery/pulls" target="blank">
        <img src="https://img.shields.io/github/issues-pr/BeauvR/registery" alt="GitHub pull requests">
    </a>
    <a href="https://github.com/BeauvR/registery/issues" target="blank">
        <img src="https://img.shields.io/github/issues/BeauvR/registery" alt="GitHub issues">
    </a>
</p>

<hr>

<strong>Free and open source.</strong>

> A private registery for composer with licensing support. Written in PHP using [Laravel Framework](https://laravel.com/).

See [#contributing](#Contributing) for more details on how you can help improving this project. We're always down to improve and receive feedback.

## Features
* A private registery for composer.
* Licensing support.
* Easy to use.
* Easy to install.

## License
Please refer to [LICENSE.md](https://github.com/BeauvR/registery/blob/master/LICENSE.md) for this project's license.

## Contributors
This list only contains some of the most notable contributors. For the full list, refer to [GitHub's contributors graph](https://github.com/BeauvR/registery/graphs/contributors).
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
### Note
This project is still in development, a web interface and webhooks are not yet implemented. For now, you can only add packages and versions to the database directly.

### Adding a composer package
To add a package, you need to create a new package in the database table ``composer_packages``

### Adding a composer package version
To add a version, you need to create a new version in the database table ``composer_package_versions``. 
This version must be linked to a composer package. 
The version type must be a valid composer version type (``stable``, ``dev``).
The composer_json_content field must contain the whole package.json file of the package version.

### Issuing a license
To issue a license, you need to create a new license in the database table ``licenses``.
This license must contain a username and password. The password is hashed using the ``bcrypt`` function of PHP.
The license must be linked to a composer package. This can be done by creating a new row in the database table ``composer_package_license``.

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
* Webhooks.
* NPM registry.