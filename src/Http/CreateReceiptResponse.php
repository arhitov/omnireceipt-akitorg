<?php
/**
 * AkiTorg driver for Omnireceipt fiscal receipt processing library
 *
 * @link      https://github.com/arhitov/omnireceipt-akitorg
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

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
