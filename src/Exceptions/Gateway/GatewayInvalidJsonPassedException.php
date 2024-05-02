<?php

namespace Omnireceipt\AkiTorg\Exceptions\Gateway;

use JetBrains\PhpStorm\Pure;
use Throwable;

class GatewayInvalidJsonPassedException extends GatewayException
{
    #[Pure]
    public function __construct(string $message = null, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(
            $message ?? 'Invalid json or request error',
            $code,
            $previous,
        );
    }
}
