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

use Omnireceipt\AkiTorg\Exceptions\Gateway\GatewayIncorrectTokenException;
use Omnireceipt\AkiTorg\Exceptions\Gateway\GatewayPaymentRequiredException;
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
        } elseif (402 === $statusCode) {
            throw new GatewayPaymentRequiredException();
        }

        return $response;
    }
}
