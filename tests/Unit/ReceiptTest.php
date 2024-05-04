<?php

namespace Omnireceipt\AkiTorg\Tests\Unit;

use Omnireceipt\AkiTorg\Tests\Fixtures\FixtureTrait;
use Omnireceipt\AkiTorg\Tests\TestCase;

class ReceiptTest extends TestCase
{
    use FixtureTrait;

    public function testToArray()
    {
        $gateway = self::getGateway();
        $gateway->initialize(
            $this->fixtureAsArray('config_full'),
        );

        /** @var \Omnireceipt\AkiTorg\Entities\Receipt $receipt */
        $receipt = $gateway->receiptFactory(
            self::receiptValidatedParameters(),
            self::receiptItemValidatedParameters(),
        );

        $array = $receipt->toArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('state', $array);
        $this->assertEquals($array['state'], $receipt->getState()->value);
        $this->assertArrayHasKey('payment', $array);
        $this->assertNull($array['payment']);
    }
}