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

use Omnireceipt\Common\Entities\Seller as BaseSeller;

/**
 * @method string getUuid() // Идентификатор организации поставщика
 * @method string getUuidOrNull() // Идентификатор организации поставщика
 * @method self setUuid(string $value)
 *
 * @method string getName() // Наименование организации поставщика
 * @method string getNameOrNull() // Наименование организации поставщика
 * @method self setName(string $value)
 *
 * @method string getInn() // ИНН организации поставщика
 * @method string getInnOrNull() // ИНН организации поставщика
 * @method self setInn(string $value)
 *
 * @method string getSite() // Адрес интернет-магазина
 * @method string getSiteOrNull() // Адрес интернет-магазина
 * @method self setSite(string $value)
 *
 * @method string getTs() // Система налогообложения
 * @method string getTsOrNull() // Система налогообложения
 * @method self setTs(string $value)
 */
class Seller extends BaseSeller
{
    static public function rules(): array
    {
        return [
            'uuid' => ['required', 'string'],
            'name' => ['required', 'string'],
            'inn'  => ['required', 'string'],
            'site' => ['required', 'string'],
            'ts'   => ['required', 'string'],
        ];
    }
}
