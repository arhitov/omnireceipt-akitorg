<?php

namespace Omnireceipt\AkiTorg\Tests;

use Omnireceipt\AkiTorg\Gateway;
use Omnireceipt\Omnireceipt;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class TestCase extends BaseTestCase
{
    use MockeryPHPUnitIntegration;

    const OMNIRECEIPT_NAME = 'AkiTorg';

    public static function getGateway(): Gateway
    {
        /** @var Gateway $gateway */
        $gateway = Omnireceipt::create(self::OMNIRECEIPT_NAME);
        return $gateway;
    }
}
