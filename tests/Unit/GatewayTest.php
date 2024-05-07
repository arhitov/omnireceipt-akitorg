<?php
/**
 * AkiTorg driver for Omnireceipt fiscal receipt processing library
 *
 * @link      https://github.com/arhitov/omnireceipt-akitorg
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\AkiTorg\Tests\Unit;

use Omnireceipt\AkiTorg\Entities\Customer;
use Omnireceipt\AkiTorg\Entities\Seller;
use Omnireceipt\AkiTorg\Tests\Fixtures\FixtureTrait;
use Omnireceipt\AkiTorg\Tests\TestCase;
use Omnireceipt\Common\AbstractGateway;
use Omnireceipt\Common\Contracts\CustomerInterface;
use Omnireceipt\Common\Contracts\SellerInterface;
use Omnireceipt\Common\Entities\Customer as BaseCustomer;
use Omnireceipt\Common\Entities\Seller as BaseSeller;
use PHPUnit\Framework\Attributes\Depends;

class GatewayTest extends TestCase
{
    use FixtureTrait;

    protected AbstractGateway $gateway;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gateway = self::getGateway();
        $this->gateway->initialize(
            $this->fixtureAsArray('config_full'),
        );
    }

    public function testBase()
    {
        $this->assertInstanceOf(AbstractGateway::class, $this->gateway);

        $this->assertEquals(self::OMNIRECEIPT_NAME, $this->gateway->getName());
        $this->assertEquals(self::OMNIRECEIPT_NAME, $this->gateway->getShortName());

        $this->assertTrue($this->gateway->validate());

        // Customer
        $customerName = $this->getFixture()->user()->name();
        $customer = $this->gateway->customerFactory([
            'name' => $customerName,
            'email' => $this->getFixture()->user()->email(),
        ]);
        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertInstanceOf(BaseCustomer::class, $customer);
        $this->assertInstanceOf(CustomerInterface::class, $customer);
        $this->assertEquals($customerName, $customer->getName());
        $this->assertTrue($customer->validate());
        $this->assertNull($this->gateway->getCustomer());
        $this->gateway->setCustomer($customer);
        $this->assertEquals($customer, $this->gateway->getCustomer());

        // Seller
        $seller = $this->gateway->sellerFactory();
        $this->assertInstanceOf(Seller::class, $seller);
        $this->assertInstanceOf(BaseSeller::class, $seller);
        $this->assertInstanceOf(SellerInterface::class, $seller);
        $this->assertTrue($seller->validate());
        $this->gateway->setSeller($seller);
        $this->assertEquals($seller, $this->gateway->getSeller());

        $receipt = $this->gateway->receiptFactory(
            [
                'doc_date' => '2024-04-30 00:28:01',
                'doc_num' => '1',
            ],
            ['amount' => 123.45],
            ['amount' => 345.67],
        );
        $receipt->setCustomer($customer);

        $this->assertTrue($receipt->validate());
        $this->assertEquals(469.12, $receipt->getAmount());
    }

    /**
     * @depends testBase
     * @return void
     */
    #[Depends('testBase')]
    public function testRestoreReceipt()
    {
        $receiptArray = $this->fixtureAsArray('receipt');
        $receipt = $this->gateway->receiptRestore($receiptArray);
        $this->assertTrue($receipt->validate());

        $this->assertEquals($receiptArray['pay_type'], $receipt->getPayType());
        $this->assertEquals($receiptArray['uuid'], $receipt->getUuid());
        $this->assertEquals($receiptArray['date'], $receipt->getDate());
        $this->assertEquals($receiptArray['firm_uuid'], $receipt->getFirmUuid());
        $this->assertEquals($receiptArray['firm_name'], $receipt->getFirmName());
        $this->assertEquals($receiptArray['firm_inn'], $receipt->getFirmInn());
        $this->assertEquals($receiptArray['firm_ts'], $receipt->getFirmTs());
        $this->assertEquals($receiptArray['doc_date'], $receipt->getDocDate());
        $this->assertEquals($receiptArray['doc_num'], $receipt->getDocNum());
        $this->assertEquals($receiptArray['@state'], $receipt->getState()->value);

        $this->assertEquals($receiptArray['@seller'], $receipt->getSeller()->toArray());
        $this->assertEquals($receiptArray['@customer'], $receipt->getCustomer()->toArray());
        $this->assertEquals($receiptArray['@itemList'][0], $receipt->getItemList()->first()->toArray());
        $this->assertNull($receipt->getPayment());

        $receiptArray = $this->fixtureAsArray('receipt_confirmed');
        $receipt = $this->gateway->receiptRestore($receiptArray);
        $this->assertTrue($receipt->validate());
        $this->assertEquals($receiptArray['@payment'], $receipt->getPayment()->toArray());
    }
}
