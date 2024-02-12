<?php

namespace Tests\Feature\Http\Middleware;

use App\Http\Controllers\Controller;
use App\Http\Middleware\Ajax;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Tests\TestCase;

class AjaxTest extends TestCase
{
    protected string $responseContent = 'Test Content';

    public function testNonAjaxRequest(): void
    {
        $request = Request::create('/');
        $next = fn() => new Response($this->responseContent);

        $middleware = new Ajax();
        $response = $middleware->handle($request, $next);

        $this->assertEquals(HttpFoundationResponse::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals(Controller::MESSAGE_UNAUTHORIZED, $response->getContent());
    }

    public function testAjaxRequest(): void
    {
        $request = Request::create('/');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $next = fn() => new Response($this->responseContent);

        $middleware = new Ajax();
        $response = $middleware->handle($request, $next);

        $this->assertEquals(HttpFoundationResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($this->responseContent, $response->getContent());
    }
}
