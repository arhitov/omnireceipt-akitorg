<?php

namespace Omnireceipt\AkiTorg\Http;

use Doctrine\Common\Collections\ArrayCollection;
use Omnireceipt\AkiTorg\Entities\Receipt;
use Omnireceipt\AkiTorg\Entities\ReceiptItem;
use Omnireceipt\Common\Http\Response\AbstractListReceiptsResponse;

class ListReceiptsResponse extends AbstractListReceiptsResponse
{
    use BaseResponseTrait;

    public function getList(): ArrayCollection
    {
        if (! $this->isSuccessful()) {
            return new ArrayCollection;
        }

        $data = $this->getData();

        if ($data instanceof ArrayCollection) {
            return $data;
        }

        $payload = $this->getPayload();

        if (! is_array($payload)) {
            return new ArrayCollection;
        }

        $collection = new ArrayCollection;

        foreach ($payload as $item) {
            $goods = $item['goods'] ?? [];
            unset($item['goods']);
            $receipt = new Receipt($item);
            $receipt->setState(static::RECEIPT_STATE_ENUM);
            foreach ($goods as $good) {
                $receipt->addItem(
                    new ReceiptItem($good)
                );
            }
            $collection->add($receipt);
        }

        return $collection;
    }
}
