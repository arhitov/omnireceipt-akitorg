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

use Omnireceipt\Common\Entities\ReceiptItem as BaseReceiptItem;

/**
 * @method int getUuid() // Идентификатор товара (необходимый атрибут)
 * @method int getUuidOrNull() // Идентификатор товара (необходимый атрибут)
 * @method self setUuid(int $value)
 *
 * @method string getCode() // Код товара
 * @method string|null getCodeOrNull() // Код товара
 * @method self setCode(string $value)
 *
 * @method int getProductType() // Тип товара: NORMAL - товар, SERVICE - услуга, TOBACCO_MARKED - Маркированный табак, SHOES_MARKED - Маркированная обувь, MEDICINE_MARKED - Маркированные лекарства, TYRES_MARKED - Шины, PERFUME_MARKED - Парфюмерия, PHOTOS_MARKED - Фототовары, LIGHT_INDUSTRY_MARKED - Легкая промышленность, TOBACCO_PRODUCTS_MARKED - Альтернативный табак, DAIRY_MARKED - Молочная продукция, WATER_MARKED - Упакованая вода, BIKE_MARKED - Велосипеды
 * @method int getProductTypeOrNull() // Тип товара: NORMAL - товар, SERVICE - услуга, TOBACCO_MARKED - Маркированный табак, SHOES_MARKED - Маркированная обувь, MEDICINE_MARKED - Маркированные лекарства, TYRES_MARKED - Шины, PERFUME_MARKED - Парфюмерия, PHOTOS_MARKED - Фототовары, LIGHT_INDUSTRY_MARKED - Легкая промышленность, TOBACCO_PRODUCTS_MARKED - Альтернативный табак, DAIRY_MARKED - Молочная продукция, WATER_MARKED - Упакованая вода, BIKE_MARKED - Велосипеды
 * @method self setProductType(int $value)
 *
 * @method int|float getPrice() // Цена (необходимый атрибут)
 * @method int|float|null getPriceOrNull() // Цена (необходимый атрибут)
 * @method self setPrice(int|float $value)
 *
 * @method string getUnitUuid() // Идентификатор единицы измерения товара
 * @method string getUnitUuidOrNull() // Идентификатор единицы измерения товара
 * @method self setUnitUuid(string $value)
 *
 * @method string getUnit() // Наименование единицы измерения
 * @method string getUnitOrNull() // Наименование единицы измерения
 * @method self setUnit(string $value)
 *
 * @method int|float getVatRate() // Ставка НДС
 * @method int|float|null getVatRateOrNul() // Ставка НДС
 * @method self setVatRate(int|float $value)
 *
 * @method int|float getVatSum() // Сумма НДС
 * @method int|float getVatSumOrNull() // Сумма НДС
 * @method self setVatSum(int|float $value)
 *
 * @method string getKomitentName() // Наименование комитента
 * @method string|null getKomitentNameOrNull() // Наименование комитента
 * @method self setKomitentName(string $value)
 *
 * @method string getKomitentInn() // ИНН комитента
 * @method string|null getKomitentInnOrNull() // ИНН комитента
 * @method self setKomitentInn(string $value)
 *
 * @method string getKomitentPhone() // Телефон комитента
 * @method string|null getKomitentPhoneOrNull() // Телефон комитента
 * @method self setKomitentPhone(string $value)
 *
 * @method string getMark() // код Data Matrix
 * @method string|null getMarkOrNull() // код Data Matrix
 * @method self setMark(string $value)
 *
 * @method int getExcise() // Акциз (тег 1229)
 * @method int|null getExciseOrNull() // Акциз (тег 1229)
 * @method self setExcise(int $value)
 *
 * @method string getCountryCode() // Код страны происхождения (тег 1230)
 * @method string|null getCountryCodeOrNull() // Код страны происхождения (тег 1230)
 * @method self setCountryCode(string $value)
 *
 * @method string getGtd() // Номер ГТД (тег 1231)
 * @method string|null getGtdOrNull() // Номер ГТД (тег 1231)
 * @method self setGtd(string $value)
 *
 * @method int getTag1214() // Признак способа расчета согласно ФФД 1.05 и выше.
 * @method int getTag1214OrNull() // Признак способа расчета согласно ФФД 1.05 и выше.
 * @method self setTag1214(int $value)
 */
class ReceiptItem extends BaseReceiptItem
{
    static public function rules(): array
    {
        return [
            'uuid'         => ['nullable', 'string'],
            'name'         => ['required', 'string'],
            'code'         => ['nullable', 'string'],
            'product_type' => ['required', 'in:NORMAL,SERVICE,TOBACCO_MARKED,SHOES_MARKED,MEDICINE_MARKED,TYRES_MARKED,PERFUME_MARKED,PHOTOS_MARKED,LIGHT_INDUSTRY_MARKED,TOBACCO_PRODUCTS_MARKED,DAIRY_MARKED,WATER_MARKED,BIKE_MARKED'],
            'amount'       => ['required', 'numeric'],
            'currency'     => ['required', 'string'],
            'quantity'     => ['required', 'numeric'],
            'unit'         => ['required', 'string'],
            'unit_uuid'    => ['required', 'string'],
        ];
    }
}
