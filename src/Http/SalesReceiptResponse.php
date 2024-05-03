<?php

namespace Omnireceipt\AkiTorg\Http;

use Doctrine\Common\Collections\ArrayCollection;
use Omnireceipt\AkiTorg\Entities\Receipt;
use Omnireceipt\AkiTorg\Entities\ReceiptItem;
use Omnireceipt\AkiTorg\Enums\ReceiptStateEnum;
use Omnireceipt\Common\Http\Response\AbstractListReceiptsResponse;

/**
 * Получить документы(заказы) за период из облака приложения.
 * Тут содержится документы для которых уже созданы и ещё не созданы чеки.
 */
class SalesReceiptResponse extends AbstractListReceiptsResponse
{
    use BaseResponseTrait;

    /**
     * @return ArrayCollection<int, Receipt>
     */
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
            $receipt->setState(ReceiptStateEnum::Pending);
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
