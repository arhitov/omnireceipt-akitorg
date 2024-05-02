<?php

namespace Omnireceipt\AkiTorg\Http;

use Omnireceipt\AkiTorg\Enums\ReceiptStateEnum;

class PaymentsReceiptResponse extends ListReceiptsResponse
{
    const RECEIPT_STATE_ENUM = ReceiptStateEnum::Successful;
}
