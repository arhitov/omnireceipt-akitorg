<?php

namespace Omnireceipt\AkiTorg\Enums;

enum ReceiptStateEnum: string
{
    case Successful = 'successful';
    case Pending = 'pending';
}
