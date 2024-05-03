<?php

namespace Omnireceipt\AkiTorg\Http;

use Omnireceipt\AkiTorg\Supports\Helper;
use Omnireceipt\Common\Http\Response\AbstractResponse;

/**
 * Получить документы(заказы) за период из облака приложения.
 * Тут содержится документы для которых уже созданы и ещё не созданы чеки.
 */
class SalesReceiptRequest extends AbstractListReceiptsRequest
{
    protected string $endpoint = 'https://epsapi.akitorg.ru/api/v1/stores/{store_uuid}/sales';

    public function sendData(array $data): AbstractResponse
    {
        $options = [
            'date_from' => Helper::dateFormattingForSend($data['date_from']),
            'date_to'   => Helper::dateFormattingForSend($data['date_to']),
            'deleted'   => $data['deleted'],
        ];

        $response = $this->request([$options]);

        return new SalesReceiptResponse($this, $response->getBody(), $response->getStatusCode());
    }
}
