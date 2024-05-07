<?php

namespace Omnireceipt\AkiTorg\Tests\Unit;

use Carbon\Carbon;
use Omnireceipt\AkiTorg\Supports\Helper;
use Omnireceipt\AkiTorg\Tests\TestCase;

class HelperTest extends TestCase
{
    public function testDateFormattingForSend()
    {
        $date = '2024-05-07 13:55:59';
        $this->assertEquals($date, Helper::dateFormattingForSend($date));

        $date = Carbon::now();
        $this->assertEquals($date->format('Y-m-d H:i:s'), Helper::dateFormattingForSend($date->toString()));

        $this->assertEquals('2024-05-07 17:19:41', Helper::dateFormattingForSend('Tue May 07 2024 17:19:41 GMT+0300'));
    }
}
