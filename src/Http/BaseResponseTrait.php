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
            400 => throw new GatewayExceptions\GatewayInvalidJsonPassedException(),
            401 => throw new GatewayExceptions\GatewayIncorrectTokenException(),
            409 => throw new GatewayExceptions\GatewayIncorrectStoreUUIDException(),
            default => $this,
        };
    }

    public function getPayload(): ?array
    {
        return json_decode($this->getData(), true);
    }
}
