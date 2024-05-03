<?php

namespace Omnireceipt\AkiTorg\Tests\Feature;

use Omnireceipt\AkiTorg\Entities\Receipt;
use Omnireceipt\AkiTorg\Gateway;
use Omnireceipt\AkiTorg\Tests\Fixtures\FixtureTrait;
use Omnireceipt\AkiTorg\Tests\MockTestCase;
use Omnireceipt\Common\AbstractGateway;
use Omnireceipt\Omnireceipt;

class ListReceiptsTest extends MockTestCase
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
            'Response_Failure_400.txt'
        ]);

        $response = $this->gateway->listReceipts([
            'date_from' => '2024-04-25 00:00:00',
            'date_to'   => '2024-04-30 23:59:59',
            'deleted'   => false,
        ]);

        $this->assertFalse($response->isSuccessful());
        $this->assertEquals(0, $response->getList()->count());
    }

    /**
     * @return void
     * @throws \Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException
     */
    public function testSuccessful()
    {
        $this->setMockHttpResponse([
            'Sales_Successful.txt',
            'Payments_Successful.txt',
        ]);

        $response = $this->gateway->listReceipts([
            'date_from' => '2024-04-25 00:00:00',
            'date_to'   => '2024-04-30 23:59:59',
        ]);

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(1, $response->getList()->count());

        $receipt = $response->getList()->first();
        $this->assertInstanceOf(Receipt::class, $receipt);
        $this->assertTrue($receipt->isSuccessful());
    }

    /**
     * @return void
     * @throws \Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException
     */
    public function testPending()
    {
        $this->setMockHttpResponse([
            'Sales_Successful.txt',
            'Payments_Successful_Another.txt',
        ]);

        $response = $this->gateway->listReceipts([
            'date_from' => '2024-04-25 00:00:00',
            'date_to'   => '2024-04-30 23:59:59',
        ]);

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(1, $response->getList()->count());

        $receipt = $response->getList()->first();

        $this->assertInstanceOf(Receipt::class, $receipt);
        $this->assertTrue($receipt->isPending());
    }

    public function testTwo()
    {
        $this->setMockHttpResponse([
            'Sales_Successful_Two.txt',
            'Payments_Successful.txt',
        ]);

        $response = $this->gateway->listReceipts([
            'date_from' => '2024-04-25 00:00:00',
            'date_to'   => '2024-04-30 23:59:59',
        ]);

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(2, $response->getList()->count());

        $uuidList = $response->getList()->getKeys();

        $receiptPayments = $response->getList()->get($uuidList[0]);
        $this->assertInstanceOf(Receipt::class, $receiptPayments);
        $this->assertTrue($receiptPayments->isSuccessful());

        $receiptSales = $response->getList()->get($uuidList[1]);
        $this->assertInstanceOf(Receipt::class, $receiptSales);
        $this->assertTrue($receiptSales->isPending());
    }
}
