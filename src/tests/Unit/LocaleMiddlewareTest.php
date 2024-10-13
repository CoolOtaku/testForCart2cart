<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Middleware\LocaleMiddleware;

class LocaleMiddlewareTest extends TestCase
{
    /** @test */
    public function it_sets_the_locale_from_session()
    {
        Session::put('locale', 'fr');

        $request = Request::create('/test-url', 'GET');

        $next = function ($request) {
            return response('next middleware');
        };

        $middleware = new LocaleMiddleware();

        $response = $middleware->handle($request, $next);

        $this->assertEquals('fr', App::getLocale());

        $this->assertEquals('next middleware', $response->getContent());
    }

    /** @test */
    public function it_sets_default_locale_if_no_locale_in_session()
    {
        Session::forget('locale');

        $request = Request::create('/test-url', 'GET');

        $next = function ($request) {
            return response('next middleware');
        };

        $middleware = new LocaleMiddleware();

        $response = $middleware->handle($request, $next);

        $this->assertEquals(config('app.locale'), App::getLocale());

        $this->assertEquals('next middleware', $response->getContent());
    }
}
