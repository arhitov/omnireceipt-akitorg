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
}
