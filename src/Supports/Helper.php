<?php
/**
 * AkiTorg driver for Omnireceipt fiscal receipt processing library
 *
 * @link      https://github.com/arhitov/omnireceipt-akitorg
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\AkiTorg\Supports;

use Carbon\Carbon;

class Helper
{
    public static function dateFormattingForSend(string|Carbon $date): string
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        return $date->format('Y-m-d H:i:s');
    }
}
