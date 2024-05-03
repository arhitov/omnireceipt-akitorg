<?php

namespace Omnireceipt\AkiTorg\Enums;

enum ReceiptStateEnum: string
{
    case Unknown = 'unknown';
    case Saved = 'saved';
    case Successful = 'successful';
    case Pending = 'pending';
}
