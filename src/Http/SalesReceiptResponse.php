<?php

namespace Omnireceipt\AkiTorg\Http;

use Omnireceipt\AkiTorg\Enums\ReceiptStateEnum;

class SalesReceiptResponse extends ListReceiptsResponse
{
    const RECEIPT_STATE_ENUM = ReceiptStateEnum::Pending;
}
