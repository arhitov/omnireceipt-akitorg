<?php

namespace Omnireceipt\AkiTorg\Tests\Feature;

use Omnireceipt\AkiTorg\Exceptions\Gateway as GatewayExceptions;
use Omnireceipt\AkiTorg\Gateway;
use Omnireceipt\AkiTorg\Tests\Fixtures\FixtureTrait;
use Omnireceipt\AkiTorg\Tests\MockTestCase;
use Omnireceipt\Common\AbstractGateway;
use Omnireceipt\Omnireceipt;

class FailureTest extends MockTestCase
{
    use FixtureTrait;

    protected AbstractGateway $gateway;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var Gateway $gateway */
        $gateway = Omnireceipt::create(
            self::OMNIRECEIPT_NAME,
            $this->getHttpClient(),
        );
        $this->gateway = $gateway;
        $this->gateway->initialize(
            $this->fixtureAsArray('config_small')
        );
    }

    public function testListReceipts400Failure()
    {
        $this->setMockHttpResponse([
            'Response_Failure_400.txt',
            'Response_Failure_400.txt',
        ]);

        $this->expectException(GatewayExceptions\GatewayInvalidJsonPassedException::class);
        $this->gateway->listReceipts([
            'date_from' => '2024-04-25 00:00:00',
            'date_to'   => '2024-04-30 23:59:59',
        ])->orFail();
    }

    public function testListReceipts401Failure()
    {
        $this->setMockHttpResponse([
            'Response_Failure_401.txt',
            'Response_Failure_401.txt',
        ]);

        $this->expectException(GatewayExceptions\GatewayIncorrectTokenException::class);
        $this->gateway->listReceipts([
            'date_from' => '2024-04-25 00:00:00',
            'date_to'   => '2024-04-30 23:59:59',
        ])->orFail();
    }

    public function testListReceipts409Failure()
    {
        $this->setMockHttpResponse([
            'Response_Failure_409.txt',
            'Response_Failure_409.txt',
        ]);

        $this->expectException(GatewayExceptions\GatewayIncorrectStoreUUIDException::class);
        $this->gateway->listReceipts([
            'date_from' => '2024-04-25 00:00:00',
            'date_to'   => '2024-04-30 23:59:59',
        ])->orFail();
    }
}
