<?php
/**
 * AkiTorg driver for Omnireceipt fiscal receipt processing library
 *
 * @link      https://github.com/arhitov/omnireceipt-akitorg
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\AkiTorg;

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Omnireceipt\AkiTorg\Entities\Receipt;
use Omnireceipt\AkiTorg\Entities\ReceiptConfirmed;
use Omnireceipt\AkiTorg\Enums\ReceiptStateEnum;
use Omnireceipt\AkiTorg\Exceptions\Gateway\GatewayException;
use Omnireceipt\AkiTorg\Http\CreateReceiptRequest;
use Omnireceipt\AkiTorg\Http\DetailsReceiptResponse;
use Omnireceipt\AkiTorg\Http\ListReceiptsResponse;
use Omnireceipt\AkiTorg\Http\PaymentsReceiptRequest;
use Omnireceipt\AkiTorg\Http\PaymentsReceiptResponse;
use Omnireceipt\AkiTorg\Http\SalesReceiptRequest;
use Omnireceipt\AkiTorg\Http\SalesReceiptResponse;
use Omnireceipt\AkiTorg\Supports\Helper;
use Omnireceipt\Common\AbstractGateway;
use Omnireceipt\AkiTorg\Entities\Customer;
use Omnireceipt\AkiTorg\Entities\Seller;
use Omnireceipt\Common\Http\Response\AbstractDetailsReceiptResponse;
use Omnireceipt\Common\Http\Response\AbstractListReceiptsResponse;
use Symfony\Component\Uid\Uuid;

class Gateway extends AbstractGateway
{
    public static function rules(): array
    {
        return [
            'key_access' => ['required', 'string'],
            'user_id'    => ['required', 'numeric'],
            'store_uuid' => ['required', 'string'],
        ];
    }

    public function getName(): string
    {
        return 'AkiTorg';
    }

    //########
    // Seller
    //########

    public function classNameSeller(): string
    {
        return Seller::class;
    }

    public function getDefaultParametersSeller(): array
    {
        $properties = $this->getParameters()['default_properties']['seller'] ?? [];
        $properties['uuid'] ??= Uuid::v4()->toRfc4122();
        return $properties;
    }

    //##########
    // Customer
    //##########

    public function classNameCustomer(): string
    {
        return Customer::class;
    }

    public function getDefaultParametersCustomer(): array
    {
        $properties = $this->getParameters()['default_properties']['customer'] ?? [];
        $properties['uuid'] ??= Uuid::v4()->toRfc4122();
        return $properties;
    }

    //#########################
    // Receipt and ReceiptItem
    //#########################

    public function classNameReceipt(): string
    {
        return Receipt::class;
    }

    public function getDefaultParametersReceipt(): array
    {
        $properties = $this->getParameters()['default_properties']['receipt'] ?? [];

        $properties['uuid'] ??= Uuid::v4()->toRfc4122();
        $properties['date'] ??= Helper::dateFormattingForSend(Carbon::now());

        $seller = $this->getSeller();
        $properties['firm_uuid'] ??= $seller->getUuidOrNull(); // Идентификатор организации поставщика
        $properties['firm_name'] ??= $seller->getNameOrNull(); // Наименование организации поставщика
        $properties['firm_inn'] ??= $seller->getInnOrNull(); // ИНН организации поставщика
        $properties['firm_address'] ??= $seller->getAddressOrNull(); // Адрес интернет-магазина
        $properties['firm_ts'] ??= $seller->getTsOrNull(); // Система налогообложения

        $customer = $this->getCustomer();
        if ($customer) {
            $properties['client_uuid'] ??= $customer->getUuidOrNull(); // Идентификатор покупателя (необходимый атрибут)
            $properties['client_name'] ??= $customer->getNameOrNull(); // Наименование покупателя (необходимый атрибут, тег 1227)
            $properties['client_inn'] ??= $customer->getInnOrNull(); // ИНН покупателя (тег 1228)
            $properties['client_type'] ??= $customer->getTypeOrNull(); // Тип покупателя: 0 - юр.лицо, 1 - индивидуальный предприниматель, 2 - физ.лицо
            $properties['client_doctype'] ??= $customer->getDoctypeOrNull(); // Код вида документа покупателя (тег 1245)
            $properties['client_docnum'] ??= $customer->getDocnumOrNull(); // Номер документа покупателя (тег 1246)
            $properties['client_birth'] ??= $customer->getBirthOrNull(); // Дата рождения покупателя в формате ГГГГ-ММ-ДД (тег 1243)
            $properties['emailphone'] ??= $customer->getEmailOrNull() ?? $customer->getPhoneOrNull(); // Email или телефон покупателя (необходимый атрибут)
        }

        return array_filter($properties);
    }

    public function receiptRestore(array $array, string $className = null, string $classItemName = null): Receipt
    {
        $parameters = $array;
        unset(
            $parameters['@state'],
            $parameters['@payment'],
        );

        /** @var Receipt $receipt */
        $receipt = parent::receiptRestore($parameters, $className, $classItemName);

        if (! empty($array['@state'])) {
            $receipt->setState(ReceiptStateEnum::from($array['@state']));
        }

        if (! empty($array['@payment'])) {
            $receipt->setPayment($this->receiptConfirmedRestore($array['@payment']));
        }

        return $receipt;
    }

    public function receiptConfirmedRestore(array $array): ReceiptConfirmed
    {
        $parameters = $array;
        unset(
            $parameters['@state'],
        );

        /** @var ReceiptConfirmed $receipt */
        $receipt = parent::receiptRestore($parameters, ReceiptConfirmed::class);

        if (! empty($array['@state'])) {
            $receipt->setState(ReceiptStateEnum::from($array['@state']));
        }

        return $receipt;
    }

    public function getDefaultParametersReceiptItem(): array
    {
        $properties = $this->getParameters()['default_properties']['receipt_item'] ?? [];
        $properties['uuid'] ??= Uuid::v4()->toRfc4122();
        return $properties;
    }

    //######################
    // HTTP Request Methods
    //######################

    public function classNameCreateReceiptRequest(): string
    {
        return CreateReceiptRequest::class;
    }

    public function classNameListReceiptsRequest(): string
    {
        throw new GatewayException('Implementation is different');
    }
    public function classNameDetailsReceiptRequest(): string
    {
        throw new GatewayException('Implementation is different');
    }
    public function classNamePaymentsReceiptsRequest(): string
    {
        return PaymentsReceiptRequest::class;
    }
    public function classNameSalesReceiptsRequest(): string
    {
        return SalesReceiptRequest::class;
    }


    /**
     * Get a list of receipts
     * AkiTorg stores sent but not posted and posted receipts in different places, so we make two requests.
     *
     * @param array $options
     * @return AbstractListReceiptsResponse
     * @throws \Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException
     */
    public function listReceipts(array $options = []): AbstractListReceiptsResponse
    {
        /** @var SalesReceiptResponse $responseSales */
        $responseSales = $this->createRequest($this->classNameSalesReceiptsRequest(), $options)->send();

        /** @var PaymentsReceiptResponse $responsePayments */
        $responsePayments = $this->createRequest($this->classNamePaymentsReceiptsRequest(), $options)->send();

        $collection = new ArrayCollection;

        foreach ($responseSales->getList() as $receipt) {
            $collection->set($receipt->getUuid(), $receipt);
        }
        foreach ($responsePayments->getList() as $receiptConfirmed) {
            /** @var Receipt $receiptSales */
            $receiptSales = $collection->get($receiptConfirmed->getSaleUuid());
            if (! is_null($receiptSales)) {
                $receiptSales->setPayment($receiptConfirmed);
            }
        }

        return new ListReceiptsResponse(
            $responseSales->getRequest(),
            $collection,
            $responseSales->getCode(),
        );
    }

    /**
     * Get check details
     * AkiTorg does not have direct access to information on the receipt,
     * so a sample is made for the period and the receipt is searched.
     * He is trying to find a receipt in the pool for the last 24 hours.
     *
     * @param string $id UUID
     * @return AbstractDetailsReceiptResponse
     * @throws \Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException
     */
    public function detailsReceipt(string $id): AbstractDetailsReceiptResponse
    {
        $options = [
            'date_from' => Helper::dateFormattingForSend(Carbon::now()->subDays(1)->startOfDay()),
            'date_to'   => Helper::dateFormattingForSend(Carbon::now()->endOfDay()),
            'deleted'   => false,
        ];

        /** @var SalesReceiptResponse $responseSales */
        $responseSales = $this->createRequest($this->classNameSalesReceiptsRequest(), $options)->send();

        /** @var Receipt|null $receiptSales */
        $receiptSales = null;
        foreach ($responseSales->getList() as $receipt) {
            if ($receipt->getUuid() == $id) {
                $receiptSales = $receipt;
                break;
            }
        }

        if (is_null($receiptSales)) {
            return new DetailsReceiptResponse($responseSales->getRequest(), null, $responseSales->getCode());
        }

        /** @var PaymentsReceiptResponse $responsePayments */
        $responsePayments = $this->createRequest($this->classNamePaymentsReceiptsRequest(), $options)->send();

        foreach ($responsePayments->getList() as $receiptPayment) {
            if ($receiptPayment->getSaleUuid() == $id) {
                $receiptSales->setPayment($receiptPayment);
                break;
            }
        }

        return new DetailsReceiptResponse($responseSales->getRequest(), $receiptSales, $responseSales->getCode());
    }
}
