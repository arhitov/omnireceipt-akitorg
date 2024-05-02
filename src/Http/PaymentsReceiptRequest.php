<?php

namespace Omnireceipt\AkiTorg\Http;

use Omnireceipt\AkiTorg\Supports\Helper;
use Omnireceipt\Common\Http\Response\AbstractResponse;

class PaymentsReceiptRequest extends AbstractListReceiptsRequest
{
    protected string $endpoint = 'https://epsapi.akitorg.ru/api/v1/stores/{store_uuid}/payments';

    public function sendData(array $data): AbstractResponse
    {
        $options = [
            'date_from' => Helper::dateFormattingForSend($data['date_from']),
            'date_to'   => Helper::dateFormattingForSend($data['date_to']),
            'deleted'   => $data['deleted'],
        ];

        $response = $this->request([$options]);

        return new PaymentsReceiptResponse($this, $response->getBody(), $response->getStatusCode());
    }
}
