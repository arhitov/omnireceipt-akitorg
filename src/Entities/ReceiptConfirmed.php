<?php
/**
 * AkiTorg driver for Omnireceipt fiscal receipt processing library
 *
 * @link      https://github.com/arhitov/omnireceipt-akitorg
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\AkiTorg\Entities;

use Omnireceipt\AkiTorg\Enums\ReceiptStateEnum;
use Omnireceipt\AkiTorg\Exceptions\Exception;
use Omnireceipt\Common\Entities\Receipt as BaseReceipt;

/**
 * @method string getSaleUuid() // Идентификатор документа основания оплаты
 * @method string getSaleUuidOrNull() // Идентификатор документа основания оплаты
 * @method self setSaleUuid(string $value)
 *
 * @method string getSaleDate() // Дата документа основания оплаты
 * @method string getSaleDateOrNull() // Дата документа основания оплаты
 * @method self setSaleDate(string $value)
 */
class ReceiptConfirmed extends BaseReceipt
{
    protected ReceiptStateEnum $state = ReceiptStateEnum::Successful;

    static public function rules(): array
    {
        return [
            'uuid'      => ['required', 'string'],
            'doc_date'  => ['required', 'string'],
            'doc_num'   => ['required', 'string'],
            'sale_uuid' => ['required', 'string'],
            'sale_date' => ['required', 'string'],
        ];
    }

    public function getState(): ReceiptStateEnum
    {
        return $this->state;
    }

    /**
     * @param ReceiptStateEnum $state
     * @return self
     * @throws Exception
     */
    public function setState(ReceiptStateEnum $state): self
    {
        if ($state !== ReceiptStateEnum::Successful) {
            throw new Exception('This object cannot have a different state.');
        }
        return $this;
    }

    public function isSuccessful(): bool
    {
        return true;
    }

    public function isPending(): bool
    {
        return false;
    }

    public function isCancelled(): bool
    {
        return false;
    }

    public function toArray(): array
    {
        $array = parent::toArray();

        $array['@state'] = $this->getState()->value;

        return $array;
    }
}
