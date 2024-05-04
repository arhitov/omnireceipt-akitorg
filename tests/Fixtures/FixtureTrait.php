<?php
/**
 * AkiTorg driver for Omnireceipt fiscal receipt processing library
 *
 * @link      https://github.com/arhitov/omnireceipt-akitorg
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\AkiTorg\Tests\Fixtures;

use RuntimeException;

trait FixtureTrait
{
    public function getFixture(): object
    {
        return new class {
            public function user(): object
            {
                return new class {
                    public function name(): string
                    {
                        return 'Ivanov Ivan';
                    }
                    public function email(): string
                    {
                        return 'vanya@yandex.ru';
                    }
                    public function inn(): string
                    {
                        return '123456789012';
                    }

                    public function asArray()
                    {
                        return [
                            'name' => $this->name(),
                            'email' => $this->email(),
                            'inn' => $this->inn(),
                        ];
                    }
                };
            }
        };
    }

    /**
     * @param string $name
     * @return string|array
     */
    protected function fixture(string $name): string|array
    {
        if (str_contains($name, '..')) {
            throw new RuntimeException("Bad name fixture");
        }

        $fixture = __DIR__ . '/fixture/' . $name;
        if (file_exists($fixture . '.json')) {
            return file_get_contents($fixture . '.json');
        }
        if (file_exists($fixture . '.php')) {
            return require $fixture . '.php';
        }

        throw new RuntimeException("Not found fixture \"{$name}\"");
    }

    /**
     * @param string $name
     * @return array
     */
    protected function fixtureAsArray(string $name): array
    {
        $data = $this->fixture($name);
        return is_string($data)
            ? json_decode($data, true, JSON_UNESCAPED_UNICODE)
            : $data;
    }

    public static function receiptValidatedParameters(): array
    {
        return [
            'uuid'     => '0ecab77f-7062-4a5f-aa20-35213db1397c',
            'doc_date' => '2016-08-25 13:48:01',
            'doc_num'  => 'ТД00-000001',
            'pay_type' => '1',
        ];
    }

    public static function receiptItemValidatedParameters(): array
    {
        return [
            'name'         => 'Информационные услуги',
            'code'         => 'info_goods',
            'product_type' => 'SERVICE',
            'quantity'     => 1,
            'amount'       => 123.45,
            'currency'     => 'RUB',
            'unit'         => 'шт',
            'unit_uuid'    => 'bd72d926-55bc-11d9-848a-00112f43529a',
            'vat_rate'     => 0,
            'tag1214'      => 4,
        ];
    }

    public static function receiptCustomerValidatedParameters(): array
    {
        return [
            'name'  => 'Ivanov Ivan',
            'email' => 'vanya@yandex.ru',
            'type'  => 2,
        ];
    }

    public static function receiptSellerValidatedParameters(): array
    {
        return [
            'name' => 'ООО "РОГА И КОПЫТА"',
            'inn'  => '4025452616',
            'site' => 'www.site.ru',
            'ts'   => 'SIMPLIFIED_INCOME',
        ];
    }
}
