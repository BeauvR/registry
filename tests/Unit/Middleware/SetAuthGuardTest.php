<?php

namespace Tests\Unit\Middleware;

use App\Enums\ComposerPackageVersionType;
use App\Http\Middleware\SetAuthGuard;
use Closure;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SetAuthGuardTest extends TestCase
{
    use WithFaker;

    private SetAuthGuard $middleware;

    private Request $request;

    private Closure $next;

    public function setUp(): void
    {
        parent::setUp();

        $this->middleware = new SetAuthGuard;
        $this->request = new Request;
        $this->next = static function (Request $request) {
            return new Response;
        };
    }

    public function test_the_handle_method_will_set_the_specified_auth_guard(): void
    {
        $guard = $this->faker->word;

        config([
            'auth' => [
                'guards' => [
                    $guard => [
                        'driver' => 'session',
                        'provider' => 'licenses',
                    ],
                ],
            ],
        ]);

        $this->assertNotEquals($guard, Auth::getDefaultDriver());

        $this->middleware->handle($this->request, $this->next, $guard);

        $this->assertEquals($guard, Auth::getDefaultDriver());
    }
}
