<?php

namespace Omnireceipt\AkiTorg\Http;

use Omnireceipt\Common\Http\Response\AbstractResponse;

class PaymentsReceiptRequest extends AbstractListReceiptsRequest
{
    protected string $endpoint = 'https://epsapi.akitorg.ru/api/v1/stores/{store_uuid}/payments';

    public function sendData(array $data): AbstractResponse
    {
        $options = [
            'date_from' => $data['date_from'],
            'date_to'   => $data['date_to'],
            'deleted'   => $data['deleted'],
        ];

        $response = $this->request([$options]);

        return new PaymentsReceiptResponse($this, $response->getBody(), $response->getStatusCode());
    }
}
