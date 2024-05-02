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

        $response = $this->gateway->detailsReceipt('dafadc58-e287-44a0-a7a0-492e3eb34f40');

        $this->assertFalse($response->isSuccessful());
        $this->assertNull($response->getReceipt());
    }

    public function testPending()
    {
        $this->setMockHttpResponse([
            'Payments_Successful.txt',
            'Sales_Successful.txt',
        ]);

        $response = $this->gateway->detailsReceipt('sales-9827-4772-b925-75c0b3399048');

        $this->assertTrue($response->isSuccessful());

        $receipt = $response->getReceipt();
        $this->assertInstanceOf(Receipt::class, $receipt);

        $this->assertTrue($receipt->isPending());
        $this->assertFalse($receipt->isSuccessful());
    }

    public function testSuccessful()
    {
        $this->setMockHttpResponse([
            'Payments_Successful.txt',
            'Sales_Successful.txt',
        ]);

        $response = $this->gateway->detailsReceipt('payments-9827-4772-b925-75c0b3399048');

        $this->assertTrue($response->isSuccessful());

        $receipt = $response->getReceipt();
        $this->assertInstanceOf(Receipt::class, $receipt);

        $this->assertFalse($receipt->isPending());
        $this->assertTrue($receipt->isSuccessful());
    }
}
