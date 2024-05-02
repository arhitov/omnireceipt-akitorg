<?php

namespace Omnireceipt\AkiTorg\Http;

use Omnireceipt\AkiTorg\Exceptions\Gateway\GatewayIncorrectTokenException;
use Psr\Http\Message\ResponseInterface;

trait BaseRequestTrait
{
    abstract public function getEndpoint(): string;
    abstract protected function getRequestMethod(): string;

    protected function getRequestUrl(array $queryParams = null): string
    {
        $url = str_replace(
            '{store_uuid}',
            $this->getParameter('store_uuid'),
            $this->getEndpoint()
        );

        if ($queryParams) {
            ksort($queryParams);
            $url .= '?' . http_build_query($queryParams);
        }

        return $url;
    }

    protected function getRequestHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'X-Authorization' => $this->getParameter('key_access'),
        ];
    }

    protected function request($body): ResponseInterface
    {
        $response = $this->httpClient->request(
            $this->getRequestMethod(),
            $this->getRequestUrl(),
            $this->getRequestHeaders(),
            is_array($body) ? json_encode($body) : $body,
        );

        $statusCode = $response->getStatusCode();
        if (401 === $statusCode) {
            throw new GatewayIncorrectTokenException();
        }

        return $response;
    }

    protected function decode($data)
    {
        return json_decode($data, true);
    }
}
