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

use Omnireceipt\AkiTorg\Entities\Receipt;
use Omnireceipt\Common\Http\Response\AbstractDetailsReceiptResponse;

class DetailsReceiptResponse extends AbstractDetailsReceiptResponse
{
    use BaseResponseTrait;

    /**
     * @return Receipt|null
     */
    public function getReceipt(): ?Receipt
    {
        /** @var Receipt|null $data */
        $data = $this->getData();
        if ($data instanceof Receipt) {
            return $data;
        }
        return null;
    }
}
