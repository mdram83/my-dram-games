<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RefreshCsrfTokenControllerTest extends TestCase
{

    public function testNonAjaxRequestFails(): void
    {
        $response = $this->get(route('ajax.csrf.token'));
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testAjaxRequestSuccessAndTokenRefreshed(): void
    {
        $initialResponse = $this->get('/');
        $initialToken = $initialResponse->getCookie('XSRF-TOKEN')->getValue();

        $response = $this
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->get(route('ajax.csrf.token'));
        $token = $response->getCookie('XSRF-TOKEN')->getValue();

        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEquals($initialToken, $token);
    }
}
