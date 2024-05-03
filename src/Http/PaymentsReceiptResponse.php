<?php

namespace Omnireceipt\AkiTorg\Http;

use Doctrine\Common\Collections\ArrayCollection;
use Omnireceipt\AkiTorg\Entities\ReceiptConfirmed;
use Omnireceipt\AkiTorg\Entities\ReceiptItem;
use Omnireceipt\AkiTorg\Enums\ReceiptStateEnum;
use Omnireceipt\Common\Http\Response\AbstractListReceiptsResponse;

/**
 * Получить оплаты(чеки) созданные на основании документов(заказов) из облака приложения.
 * На этом моменте чек уже создан.
 */
class PaymentsReceiptResponse extends AbstractListReceiptsResponse
{
    use BaseResponseTrait;

    /**
     * @return ArrayCollection<int, ReceiptConfirmed>
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

            $receipt = new ReceiptConfirmed($item);
            $receipt->setState(ReceiptStateEnum::Successful);
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
