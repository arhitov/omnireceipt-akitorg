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

use Omnireceipt\AkiTorg\Exceptions\Gateway as GatewayExceptions;

trait BaseResponseTrait
{
    /**
     * @return static
     * @throws \Omnireceipt\AkiTorg\Exceptions\Gateway\GatewayException
     */
    public function orFail(): static
    {
        return match ($this->getCode()) {
            400 => throw new GatewayExceptions\GatewayInvalidJsonPassedException($this->getMessageByData(), 400),
            401 => throw new GatewayExceptions\GatewayIncorrectTokenException($this->getMessageByData(), 401),
            402 => throw new GatewayExceptions\GatewayPaymentRequiredException($this->getMessageByData(), 402),
            409 => throw new GatewayExceptions\GatewayIncorrectStoreUUIDException($this->getMessageByData(), 409),
            default => $this,
        };
    }

    public function getPayload(): ?array
    {
        return json_decode($this->getData(), true);
    }

    protected function getMessageByData(): ?string
    {
        $data = $this->getData();
        return match (true) {
            is_object($data) && method_exists($data, 'getContents') => $data->getContents(),
            is_string($data) => $data,
            default => null,
        };
    }
}
