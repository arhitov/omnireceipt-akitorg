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
            'doc_date' => ['nullable', 'string'],
            'doc_num'  => ['nullable', 'string'],
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

        // Данные различаться не могут, а чтобы не заполнять лишний раз, копируем.
        $seller = $this->getSeller();
        if ($seller) {
            $payment->setSeller($seller);
        }

        // Данные различаться не могут, а чтобы не заполнять лишний раз, копируем.
        $customer = $this->getCustomer();
        if ($customer) {
            $payment->setCustomer($customer);
        }

        return $this;
    }

    public function getId(): string
    {
        return $this->getUuid();
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

    public function toArray(): array
    {
        $array = parent::toArray();

        $array['@state'] = $this->getState()->value;
        $array['@payment'] = $this->getPayment()?->toArray();

        return $array;
    }
}
