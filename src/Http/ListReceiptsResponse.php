<?php

namespace Omnireceipt\AkiTorg\Http;

use Doctrine\Common\Collections\ArrayCollection;
use Omnireceipt\AkiTorg\Exceptions\Gateway\GatewayException;
use Omnireceipt\Common\Http\Response\AbstractListReceiptsResponse;

class ListReceiptsResponse extends AbstractListReceiptsResponse
{
    use BaseResponseTrait;

    /**
     * @return ArrayCollection
     * @throws GatewayException
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

        throw new GatewayException('Implementation is different');
    }
}
