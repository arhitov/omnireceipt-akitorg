<?php

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