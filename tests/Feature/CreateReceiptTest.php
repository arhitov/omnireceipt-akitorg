<?php
/**
 * AkiTorg driver for Omnireceipt fiscal receipt processing library
 *
 * @link      https://github.com/arhitov/omnireceipt-akitorg
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\AkiTorg\Tests\Feature;

use Omnireceipt\AkiTorg\Gateway;
use Omnireceipt\AkiTorg\Tests\Fixtures\FixtureTrait;
use Omnireceipt\AkiTorg\Tests\MockTestCase;
use Omnireceipt\Common\AbstractGateway;
use Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException;
use Omnireceipt\Omnireceipt;

class CreateReceiptTest extends MockTestCase
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

    public function testFailureReceiptMain()
    {
        $this->setMockHttpResponse('SalesAdd_Successful.txt');

        $receipt = $this->gateway->receiptFactory(
            [
                // This required
            ],
            self::receiptItemValidatedParameters(),
        );

        $customer = $this->gateway->customerFactory(
            self::customerValidatedParameters(),
        );
        $receipt->setCustomer($customer);

        $this->expectException(ParameterValidateException::class);
        $this->gateway->createReceipt($receipt)
                      ->orFail();
    }

    public function testFailureReceiptItemNoSet()
    {
        $this->setMockHttpResponse('SalesAdd_Successful.txt');

        $receipt = $this->gateway->receiptFactory(
            self::receiptValidatedParameters(),
        );

        $customer = $this->gateway->customerFactory(
            self::customerValidatedParameters(),
        );
        $receipt->setCustomer($customer);

        $this->expectException(ParameterValidateException::class);
        $this->gateway->createReceipt($receipt)
                      ->orFail();
    }

    public function testFailureReceiptItemEmpty()
    {
        $this->setMockHttpResponse('SalesAdd_Successful.txt');

        $receipt = $this->gateway->receiptFactory(
            self::receiptValidatedParameters(),
            [
                // This required
            ],
        );

        $customer = $this->gateway->customerFactory(
            self::customerValidatedParameters(),
        );
        $receipt->setCustomer($customer);

        $this->expectException(ParameterValidateException::class);
        $this->gateway->createReceipt($receipt)
                      ->orFail();
    }

    public function testFailureReceiptCustomerNotSet()
    {
        $this->setMockHttpResponse('SalesAdd_Successful.txt');

        $receipt = $this->gateway->receiptFactory(
            self::receiptValidatedParameters(),
            self::receiptItemValidatedParameters(),
        );

        $this->expectException(ParameterValidateException::class);
        $this->gateway->createReceipt($receipt)
                      ->orFail();
    }

    public function testSuccessful()
    {
        $this->setMockHttpResponse('SalesAdd_Successful.txt');

        $receipt = $this->gateway->receiptFactory(
            self::receiptValidatedParameters(),
            self::receiptItemValidatedParameters(),
        );

        $customer = $this->gateway->customerFactory(
            self::customerValidatedParameters(),
        );
        $receipt->setCustomer($customer);

        $seller = $this->gateway->sellerFactory(
            self::sellerValidatedParameters(),
        );

        $response = $this->gateway->createReceipt($receipt, seller: $seller);

        $this->assertTrue($response->isSuccessful());
    }
}
