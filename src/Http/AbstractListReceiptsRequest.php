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

use Omnireceipt\Common\Http\Request\AbstractListReceiptRequest;

/**
 * @method string getDateFrom()
 * @method self setDateFrom(string $value)
 * @method string getDateTo()
 * @method self setDateTo(string $value)
 * @method bool getDeleted()
 * @method self setDeleted(bool $value)
 */
abstract class AbstractListReceiptsRequest extends AbstractListReceiptRequest
{
    use BaseRequestTrait;

    static public function rules(): array
    {
        return [
            'date_from' => ['required', 'string'],
            'date_to' => ['required', 'string'],
            'deleted' => ['required', 'bool'],
        ];
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

    public function getDefaultParameters(): array
    {
        return [
            'deleted' => false,
        ];
    }

    public function getData(): array
    {
        return [
            'date_from' => $this->getDateFrom(),
            'date_to' => $this->getDateTo(),
            'deleted' => $this->getDeleted(),
        ];
    }

    protected function getRequestMethod(): string
    {
        return 'POST';
    }
}
