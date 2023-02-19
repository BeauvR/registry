<?php

namespace Tests\Unit\Enums;

use App\Enums\ComposerPackageVersionType;
use PHPUnit\Framework\TestCase;

class ComposerPackageVersionTypeTest extends TestCase
{

    public function test_that_it_can_be_created_from_route_when_dev_is_in_route_param(): void
    {
        $this->assertEquals(ComposerPackageVersionType::DEV, ComposerPackageVersionType::fromRoute('~dev'));
    }

    public function test_that_it_can_be_created_from_route_when_a_empty_string_is_in_route_param(): void
    {
        $this->assertEquals(ComposerPackageVersionType::STABLE, ComposerPackageVersionType::fromRoute(''));
    }
}
