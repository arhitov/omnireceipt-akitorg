<?php

namespace Omnireceipt\AkiTorg\Entities;

use Omnireceipt\AkiTorg\Enums\ReceiptStateEnum;
use Omnireceipt\Common\Entities\Receipt as BaseReceipt;

/**
 * @method string getUuid() // Идентификатор документа
 * @method string getUuidOrNull() // Идентификатор документа
 * @method self setUuid(string $value)
 *
 * @method string getDocDate() // Дата документа (2016-08-25 13:48:01)
 * @method string getDocDateOrNull() // Дата документа (2016-08-25 13:48:01)
 * @method self setDocDate(string $value)
 *
 * @method string getDocNum() // Номер документа
 * @method string getDocNumOrNull() // Номер документа
 * @method self setDocNum(string $value)
 *
 * @method string getInfo() // Комментарий к документу
 * @method string getInfoOrNull() // Комментарий к документу
 * @method self setInfo(string $value)
 *
 * @method int getPayType()
 * @method int getPayTypeOrNull()
 * @method self setPayType(int $value)
 *
 * @method \Omnireceipt\AkiTorg\Entities\Seller getSeller()
 * @method \Omnireceipt\AkiTorg\Entities\Customer getCustomer()
 */
class Receipt extends BaseReceipt
{
    protected ?ReceiptConfirmed $payment = null;
    protected ReceiptStateEnum $state = ReceiptStateEnum::Saved;

    static public function rules(): array
    {
        return [
            'uuid'     => ['required', 'string'],
            'doc_date' => ['required', 'string'],
            'doc_num'  => ['required', 'string'],
            'info'     => ['nullable', 'string'],
            'pay_type' => ['required', 'numeric', 'in:0,1'],
        ];
    }

    public function getPayment(): ?ReceiptConfirmed
    {
        return $this->payment;
    }

    public function setPayment(ReceiptConfirmed $payment): self
    {
        $this->payment = $payment;
        $this->setState(ReceiptStateEnum::Successful);
        return $this;
    }

    public function getState(): ReceiptStateEnum
    {
        return $this->state;
    }

    public function setState(ReceiptStateEnum $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function isSuccessful(): bool
    {
        return $this->getState() === ReceiptStateEnum::Successful;
    }

    public function isPending(): bool
    {
        return $this->getState() === ReceiptStateEnum::Pending;
    }

    public function isCancelled(): bool
    {
        return false;
    }
}
