<?php

namespace Omnireceipt\AkiTorg\Entities;

use Omnireceipt\Common\Entities\Customer as BaseCustomer;

/**
 * @method string getUuid()
 * @method string getUuidOrNull()
 * @method self setUuid(string $value)
 *
 * @method string getName()
 * @method string getNameOrNull()
 * @method self setName(string $value)
 *
 * @method string getEmail()
 * @method string getEmailOrNull()
 * @method self setEmail(string $value)
 *
 * @method string getPhone()
 * @method string getPhoneOrNull()
 * @method self setPhone(string $value)
 *
 * @method string getInn()
 * @method string getInnOrNull()
 * @method self setInn(string $value)
 *
 * @method int getType()
 * @method int getTypeOrNull()
 * @method self setType(int $value)
 */
class Customer extends BaseCustomer
{
    static public function rules(): array
    {
        return [
            'uuid'  => ['required', 'string'],
            'name'  => ['required', 'string'],
            'email' => ['required', 'string'],
            'phone' => ['nullable', 'string'],
            'inn'   => ['nullable', 'string'],
            'type'  => ['required', 'numeric', 'in:0,1,2'],
        ];
    }
}
