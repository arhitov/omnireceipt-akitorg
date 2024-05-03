<?php

namespace Omnireceipt\AkiTorg\Tests\Feature;

use Omnireceipt\AkiTorg\Entities\Receipt;
use Omnireceipt\AkiTorg\Gateway;
use Omnireceipt\AkiTorg\Tests\Fixtures\FixtureTrait;
use Omnireceipt\AkiTorg\Tests\MockTestCase;
use Omnireceipt\Common\AbstractGateway;
use Omnireceipt\Omnireceipt;

class DetailsReceiptTest extends MockTestCase
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

    public function testFailure()
    {
        $this->setMockHttpResponse([
            'Response_Failure_400.txt',
            'Response_Failure_400.txt',
        ]);
        $uuid = 'find-7062-4a5f-aa20-35213db1397c';

        $response = $this->gateway->detailsReceipt($uuid);

        $this->assertFalse($response->isSuccessful());
        $this->assertNull($response->getReceipt());
    }

    public function testNotFound()
    {
        $this->setMockHttpResponse([
            'Sales_Successful_Another.txt',
            'Payments_Successful_Another.txt',
        ]);
        $uuid = 'find-7062-4a5f-aa20-35213db1397c';

        $response = $this->gateway->detailsReceipt($uuid);

        $this->assertTrue($response->isSuccessful());

        $receipt = $response->getReceipt();
        $this->assertNull($receipt);
    }

    public function testPending()
    {
        $this->setMockHttpResponse([
            'Sales_Successful.txt',
            'Payments_Successful_Another.txt',
        ]);
        $uuid = 'find-7062-4a5f-aa20-35213db1397c';

        $response = $this->gateway->detailsReceipt($uuid);

        $this->assertTrue($response->isSuccessful());

        $receipt = $response->getReceipt();
        $this->assertInstanceOf(Receipt::class, $receipt);
        $this->assertEquals($uuid, $receipt->getUuid());
        $this->assertTrue($receipt->isPending());
        $this->assertFalse($receipt->isSuccessful());
    }

    public function testSuccessful()
    {
        $this->setMockHttpResponse([
            'Sales_Successful.txt',
            'Payments_Successful.txt',
        ]);
        $uuid = 'find-7062-4a5f-aa20-35213db1397c';

        $response = $this->gateway->detailsReceipt($uuid);

        $this->assertTrue($response->isSuccessful());

        $receipt = $response->getReceipt();
        $this->assertInstanceOf(Receipt::class, $receipt);
        $this->assertEquals($uuid, $receipt->getUuid());
        $this->assertFalse($receipt->isPending());
        $this->assertTrue($receipt->isSuccessful());
    }
}
