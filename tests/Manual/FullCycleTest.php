<?php

namespace Omnireceipt\AkiTorg\Tests\Manual;

use Carbon\Carbon;
use Omnireceipt\AkiTorg\Entities\Receipt;
use Omnireceipt\AkiTorg\Gateway;
use Omnireceipt\AkiTorg\Supports\Helper;
use Omnireceipt\AkiTorg\Tests\ManualTestCase;
use Omnireceipt\Omnireceipt;
use Symfony\Component\Uid\Uuid;

class FullCycleTest extends ManualTestCase
{
    protected Gateway $gateway;
    protected static string $docNum;
    protected static string $receiptUuid;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var Gateway $gateway */
        $gateway = Omnireceipt::create(
            self::OMNIRECEIPT_NAME,
        );
        $gateway->initialize(config('omnireceipt_akitorg'));

        $this->gateway = $gateway;
    }

    public function testCreateReceipt()
    {
        self::$receiptUuid = Uuid::v4()->toRfc4122();
        self::$docNum = $this->readline('Please input doc_num: ');

        $this->assertTrue(true);

        $receipt = $this->gateway->receiptFactory(
            [
                'uuid'     => self::$receiptUuid,
                'doc_date' => Carbon::now()->toString(),
                'doc_num'  => self::$docNum,
            ],
            [
                'amount' => 1,
            ],
        );

        $customer = $this->gateway->customerFactory([
            'name' => 'Alexander Arhitov',
            'email' => 'clgsru@gmail.com',
        ]);
        $receipt->setCustomer($customer);

        $responce = $this->gateway->createReceipt($receipt);

        $this->assertTrue($responce->isSuccessful());
    }

    /**
     * @depends testCreateReceipt
     * @return void
     * @throws \Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException
     */
    #[\PHPUnit\Framework\Attributes\Depends('testCreateReceipt')]
    public function testListReceiptsPending()
    {
        /** @var Receipt|null $receiptFind */
        $receiptFind = null;

        do {
            sleep(1); // We are waiting for the data in the AkiTorg database to be updated

            $response = $this->gateway->listReceipts([
                'date_from' => Helper::dateFormattingForSend(Carbon::now()->subDays(1)->startOfDay()),
                'date_to'   => Helper::dateFormattingForSend(Carbon::now()->endOfDay()),
            ]);

            $this->assertTrue($response->isSuccessful());

            /** @var Receipt $receipt */
            foreach ($response->getList() as $receipt) {
                if ($receipt->getUuid() == self::$receiptUuid) {
                    $receiptFind = $receipt;
                    break;
                }
            }

            if (! is_null($receiptFind)) {
                break;
            }

            $this->readline('The receipt is not yet in the database. Press Enter to retry the request.');

        } while (true);

        $this->assertInstanceOf(Receipt::class, $receiptFind);

        // We assume that the check did not have time to be generated in such a short time
        $this->assertTrue($receiptFind->isPending());
    }

    /**
     * @depends testListReceiptsPending
     * @return void
     * @throws \Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException
     */
    #[\PHPUnit\Framework\Attributes\Depends('testListReceiptsPending')]
    public function testDetailsReceiptPending()
    {
        $response = $this->gateway->detailsReceipt(self::$receiptUuid);

        $this->assertTrue($response->isSuccessful());

        $receipt = $response->getReceipt();

        $this->assertInstanceOf(Receipt::class, $receipt);

        // We assume that the check did not have time to be generated in such a short time
        $this->assertTrue($receipt->isPending());
    }

    /**
     * @depends testDetailsReceiptPending
     * @return void
     * @throws \Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException
     */
    #[\PHPUnit\Framework\Attributes\Depends('testDetailsReceiptPending')]
    public function testListReceiptsSuccessful()
    {
        /** @var Receipt|null $receiptFind */
        $receiptFind = null;

        do {
            sleep(10); // We are waiting for the data in the AkiTorg database to be updated

            $response = $this->gateway->listReceipts([
                'date_from' => Helper::dateFormattingForSend(Carbon::now()->subDays(1)->startOfDay()),
                'date_to'   => Helper::dateFormattingForSend(Carbon::now()->endOfDay()),
            ]);

            $this->assertTrue($response->isSuccessful());

            /** @var Receipt $receipt */
            foreach ($response->getList() as $receipt) {
                if ($receipt->getUuidOrNull() === self::$receiptUuid) {
                    $receiptFind = $receipt;
                    break;
                }
            }

            $this->assertInstanceOf(Receipt::class, $receiptFind);

            if ($receiptFind->isSuccessful()) {
                break;
            }

            $this->readline("The receipt has not yet generated a check. Current state \"{$receiptFind->getState()->value}\". We wait. Press Enter to retry the request.");

        } while (true);

        // We assume that the check did not have time to be generated in such a short time
        $this->assertTrue($receiptFind->isSuccessful());
    }

    /**
     * @depends testListReceiptsSuccessful
     * @return void
     * @throws \Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException
     */
    #[\PHPUnit\Framework\Attributes\Depends('testListReceiptsSuccessful')]
    public function testDetailsReceiptSuccessful()
    {
        $response = $this->gateway->detailsReceipt(self::$receiptUuid);

        $this->assertTrue($response->isSuccessful());

        $receipt = $response->getReceipt();

        $this->assertInstanceOf(Receipt::class, $receipt);

        $this->assertTrue($receipt->isSuccessful());
    }
}
