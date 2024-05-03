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
}
