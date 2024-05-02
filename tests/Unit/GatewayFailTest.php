<?php

namespace Omnireceipt\AkiTorg\Tests\Unit;

use Omnireceipt\AkiTorg\Tests\TestCase;
use Omnireceipt\Common\AbstractGateway;

class GatewayFailTest extends TestCase
{
    protected AbstractGateway $gateway;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gateway = self::getGateway();
    }

    public function testBase()
    {
        $this->assertInstanceOf(AbstractGateway::class, $this->gateway);

        $this->assertFalse($this->gateway->validate());

        $parametersLastError = $this->gateway->validateLastError()['parameters'] ?? [];
        $this->assertIsArray($parametersLastError);

        $this->assertArrayHasKey('key_access', $parametersLastError);
        $this->assertArrayHasKey('user_id', $parametersLastError);
        $this->assertArrayHasKey('store_uuid', $parametersLastError);
    }
}
