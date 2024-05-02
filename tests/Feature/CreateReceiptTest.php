<?php

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
            self::receiptCustomerValidatedParameters(),
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
            self::receiptCustomerValidatedParameters(),
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
            self::receiptCustomerValidatedParameters(),
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
            self::receiptCustomerValidatedParameters(),
        );
        $receipt->setCustomer($customer);

        $seller = $this->gateway->sellerFactory(
            self::receiptSellerValidatedParameters(),
        );

        $response = $this->gateway->createReceipt($receipt, seller: $seller);

        $this->assertTrue($response->isSuccessful());
    }

    public static function receiptValidatedParameters(): array
    {
        return [
            'uuid'     => '0ecab77f-7062-4a5f-aa20-35213db1397c',
            'doc_date' => '2016-08-25 13:48:01',
            'doc_num'  => 'ТД00-000001',
            'pay_type' => '1',
        ];
    }

    public static function receiptItemValidatedParameters(): array
    {
        return [
            'name'         => 'Информационные услуги',
            'code'         => 'info_goods',
            'product_type' => 'SERVICE',
            'quantity'     => 1,
            'amount'       => 123.45,
            'currency'     => 'RUB',
            'unit'         => 'шт',
            'unit_uuid'    => 'bd72d926-55bc-11d9-848a-00112f43529a',
            'vat_rate'     => 0,
            'tag1214'      => 4,
        ];
    }

    public static function receiptCustomerValidatedParameters(): array
    {
        return [
            'name'  => 'Ivanov Ivan',
            'email' => 'vanya@yandex.ru',
            'type'  => 2,
        ];
    }

    public static function receiptSellerValidatedParameters(): array
    {
        return [
            'name' => 'ООО "РОГА И КОПЫТА"',
            'inn'  => '4025452616',
            'site' => 'www.site.ru',
            'ts'   => 'SIMPLIFIED_INCOME',
        ];
    }
}
