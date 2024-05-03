<?php
/**
 * AkiTorg driver for Omnireceipt fiscal receipt processing library
 *
 * @link      https://github.com/arhitov/omnireceipt-akitorg
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

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
