<?php

namespace Omnireceipt\AkiTorg\Entities;

use Omnireceipt\AkiTorg\Enums\ReceiptStateEnum;
use Omnireceipt\Common\Supports\ParametersTrait;

/**
 * @method string getSaleUuid() // Идентификатор документа основания оплаты
 * @method string getSaleUuidOrNull() // Идентификатор документа основания оплаты
 * @method self setSaleUuid(string $value)
 *
 * @method string getSaleDate() // Дата документа основания оплаты
 * @method string getSaleDateOrNull() // Дата документа основания оплаты
 * @method self setSaleDate(string $value)
 */
class ReceiptConfirmed extends Receipt
{
    use ParametersTrait;

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
}
