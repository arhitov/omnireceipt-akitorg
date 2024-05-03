<?php
/**
 * AkiTorg driver for Omnireceipt fiscal receipt processing library
 *
 * @link      https://github.com/arhitov/omnireceipt-akitorg
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\AkiTorg\Exceptions\Gateway;

use JetBrains\PhpStorm\Pure;
use Throwable;

class GatewayIncorrectStoreUUIDException extends GatewayException
{
    #[Pure]
    public function __construct(string $message = null, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(
            $message ?? 'Invalid storeUUID',
            $code,
            $previous,
        );
    }
}
