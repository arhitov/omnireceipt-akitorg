<?php

namespace Omnireceipt\AkiTorg\Tests;

class ManualTestCase extends TestCase
{
    public static function readline(?string $prompt): string
    {
        ob_get_flush();
        ob_start();
        return readline($prompt);
    }
}
