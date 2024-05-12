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

use Omnireceipt\AkiTorg\Supports\Helper;
use Omnireceipt\Common\Http\Request\AbstractCreateReceiptRequest;
use Omnireceipt\Common\Http\Response\AbstractResponse;

class CreateReceiptRequest extends AbstractCreateReceiptRequest
{
    use BaseRequestTrait;

    protected string $endpoint = 'https://epsapi.akitorg.ru/api/v1/stores/{store_uuid}/sales/add';

    static public function rules(): array
    {
        return [];
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function setEndpoint(string $value): self
    {
        $this->endpoint = $value;
        return $this;
    }

    public function getData(): array
    {
        $receipt = $this->getReceipt();

        $receipt->validateOrFail();

        $goods = [];
        /** @var \Omnireceipt\AkiTorg\Entities\ReceiptItem $item */
        foreach ($receipt->getItemList() as $item) {
            $goods[] = [
                'good_uuid'      => $item->getUuidOrNull(),                                                          // Строка Идентификатор товара (необходимый атрибут)
                'good_name'      => $item->getName(),                                                                // Строка Наименование товара (необходимый атрибут)
                'good_code'      => $item->getCodeOrNull(),                                                          // Строка Код товара
                'product_type'   => $item->getProductType(),                                                         // Строка Тип товара
                'quantity'       => $item->getQuantity(),                                                            // Число Количество (необходимый атрибут)
                'price'          => $item->getPriceOrNull() ?? ($item->getAmount() / $item->getQuantity()),          // Число Цена (необходимый атрибут)
                'dsum'           => $item->getAmount(),                                                              // Число Сумма (необходимый атрибут)
                'unit_uuid'      => $item->getUnitUuid(),                                                            // Строка Идентификатор единицы измерения товара
                'unit_name'      => $item->getUnit(),                                                                // Строка Наименование единицы измерения
                'vat_rate'       => $item->getVatRate(),                                                             // Число Ставка НДС
                'vat_sum'        => $item->getVatSumOrNull() ?? ($receipt->getAmount() / 100 * $item->getVatRate()), // Число Сумма НДС
                'komitent_name'  => $item->getKomitentNameOrNull(),                                                  // Строка Наименование комитента
                'komitent_inn'   => $item->getKomitentInnOrNull(),                                                   // Строка ИНН комитента
                'komitent_phone' => $item->getKomitentPhoneOrNull(),                                                 // Строка Телефон комитента
                'mark'           => $item->getMarkOrNull(),                                                          // Строка код Data Matrix
                'excise'         => $item->getExciseOrNull(),                                                        // Число Акциз (тег 1229)
                'country_code'   => $item->getCountryCodeOrNull(),                                                   // Строка Код страны происхождения (тег 1230)
                'gtd'            => $item->getGtdOrNull(),                                                           // Строка Номер ГТД (тег 1231)
                'tag1214'        => $item->getTag1214OrNull(),                                                       // Число Признак способа расчета согласно ФФД 1.05 и выше.
            ];
        }

        $seller = $receipt->getSeller();
        $customer = $receipt->getCustomer();

        return array_filter([
            'uuid'            => $receipt->getUuid(),                                  // String Идентификатор документа, required
            'doc_date'        =>  Helper::dateFormattingForSend($receipt->getDate()),  // String Дата документа, required (2016-08-25 13:48:01)
            'doc_num'         => $receipt->getDocNum(),                                // String Номер документа, required
            'client_uuid'     => $customer->getUuid(),                                 // String Идентификатор покупателя, required
            'client_name'     => $customer->getName(),                                 // String Наименование покупателя, required
            'client_inn'      => $customer->getInnOrNull(),                            // String ИНН покупателя или номер паспорта
            'client_type'     => $customer->getType(),                                 // Number Тип покупателя: 0 - юр.лицо, 1 - индивидуальный предприниматель, 2 - физ.лицо
            'dsum'            => $receipt->getAmount(),                                // Number Сумма документа, required
            'debt'            => $receipt->getAmount(),                                // Number Сумма неоплаченого долга по документу, required
            'info'            => $receipt->getInfoOrNull(),                            // String Комментарий к документу
            'emailphone'      => $customer->getPhoneOrNull() ?? $customer->getEmail(), // String Email или телефон покупателя, required
            'pay_type'        => $receipt->getPayType(),                               // Number Тип оплаты: 0 - НАЛИЧНЫЕ, 1 - ЭЛЕКТРОННО
            'firm_uuid'       => $seller->getUuid(),                                   // String Идентификатор организации поставщика
            'firm_name'       => $seller->getName(),                                   // String Наименование организации поставщика
            'firm_inn'        => $seller->getInn(),                                    // String ИНН организации поставщика
            'firm_address'    => $seller->getSite(),                                   // String Адрес интернет-магазина
            'firm_ts'         => $seller->getTs(),                                     // String Система налогообложения
            'principal_name'  => $receipt->getPrincipalNameOrNull(),                   // String Наименование принципала
            'principal_inn'   => $receipt->getPrincipalInnOrNull(),                    // String ИНН принципала
            'principal_phone' => $receipt->getPrincipalPhoneOrNull(),                  // String Телефон принципала
            'goods'           => $goods,                                               // Array Массив содержащий товары
        ]);
    }

    public function sendData(array $data): AbstractResponse
    {
        $response = $this->request([$data]);

        return new CreateReceiptResponse($this, $response->getBody(), $response->getStatusCode());
    }

    protected function getRequestMethod(): string
    {
        return 'POST';
    }
}
