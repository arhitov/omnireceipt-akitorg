<?php
/**
 * AkiTorg driver for Omnireceipt fiscal receipt processing library
 *
 * @link      https://github.com/arhitov/omnireceipt-akitorg
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\AkiTorg\Enums;

enum ReceiptStateEnum: string
{
    case Unknown = 'unknown';
    case Saved = 'saved';
    case Successful = 'successful';
    case Pending = 'pending';
}
