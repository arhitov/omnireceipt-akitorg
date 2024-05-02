<?php

namespace Omnireceipt\AkiTorg\Http;

use Omnireceipt\Common\Http\Response\AbstractCreateReceiptResponse;

class CreateReceiptResponse extends AbstractCreateReceiptResponse
{
    use BaseResponseTrait;

    public function isSuccessful(): bool
    {
        $payload = $this->getPayload();
        return 200 === $this->getCode() && array_key_exists('added', $payload);
    }
}
