<?php
/**
 * AkiTorg driver for Omnireceipt fiscal receipt processing library
 *
 * @link      https://github.com/arhitov/omnireceipt-akitorg
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

return [
    // Ключ доступа:
    'key_access' => '550e8400-e29b-41d4-a716-446655440000',
    // UserID:
    'user_id' => 123,
    // StoreUUID магазинов:
    'store_uuid' => '00112233-4455-6677-8899-aabbccddeeff',
    // Default Entities Properties
    'default_properties' => [
        'seller'       => [
            'name' => 'ООО "РОГА И КОПЫТА"',
            'inn'  => '4025452616',
            'site' => 'www.site.ru',
            'ts'   => 'SIMPLIFIED_INCOME', // 'PATENT',
        ],
        'customer'     => [
            'type' => 2, // Number	Тип покупателя: 0 - юр.лицо, 1 - индивидуальный предприниматель, 2 - физ.лицо
        ],
        'receipt'      => [
            'pay_type' => 1, // Тип оплаты: 0 - НАЛИЧНЫЕ, 1 - БЕЗНАЛИЧНЫЕ
        ],
        'receipt_item' => [
            'name'         => 'Информационные услуги',
            'code'         => 'info_goods',
            'product_type' => 'SERVICE', // Тип товара: NORMAL - товар, SERVICE - услуга, ...
            'quantity'     => 1,
            'currency'     => 'RUB',
            'unit'         => 'шт',
            'unit_uuid'    => 'bd72d926-55bc-11d9-848a-00112f43529a',
            'vat_rate'     => 0, // Без НДС
            'tag1214'      => 4, // Признак способа расчета согласно ФФД 1.05 и выше. 4 => Полная оплата, в том числе с учетом аванса (предварительной оплаты) в момент передачи предмета расчета
        ],
    ],
];
