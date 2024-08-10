<?php

namespace Rules;

use App\Rules\BetaRegistrationCode;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class BetaRegistrationCodeTest extends TestCase
{
    protected ?string $validCode = 'test-code';
    protected ValidationRule $rule;
    protected int $failCounter = 0;

    public function setUp(): void
    {
        parent::setUp();
        $this->rule = new BetaRegistrationCode();
        $this->failCounter = 0;
    }

    protected function mockConfigFacade(bool $null = false): void
    {
        Config::shouldReceive('get')
            ->once()
            ->with('auth.beta_registration_code')
            ->andReturn(!$null ? $this->validCode : null);
    }

    protected function closure(): void
    {
        $this->failCounter++;
    }

    public function testValidCode(): void
    {
        $this->mockConfigFacade();
        $this->rule->validate('name', $this->validCode, fn() => $this->closure());

        $this->assertEquals(0, $this->failCounter);
    }

    public function testInvalidCode(): void
    {
        $this->mockConfigFacade();
        $this->rule->validate('name', 'definIte7y-inv@al1D-c0d3.', fn() => $this->closure());

        $this->assertNotEquals(0, $this->failCounter);
    }

    public function testNullValueForConfiguredCode(): void
    {
        $this->mockConfigFacade();
        $this->rule->validate('name', null, fn() => $this->closure());

        $this->assertNotEquals(0, $this->failCounter);
    }

    public function testEmptyStringForConfiguredCode(): void
    {
        $this->mockConfigFacade();
        $this->rule->validate('name', '', fn() => $this->closure());

        $this->assertNotEquals(0, $this->failCounter);
    }

    public function testNullValueForNotConfiguredCode(): void
    {
        $this->mockConfigFacade(true);
        $this->rule->validate('name', null, fn() => $this->closure());

        $this->assertEquals(0, $this->failCounter);
    }

    public function testEmptyStringForNotConfiguredCode(): void
    {
        $this->mockConfigFacade(true);
        $this->rule->validate('name', '', fn() => $this->closure());

        $this->assertEquals(0, $this->failCounter);
    }
}
