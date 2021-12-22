<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 * @updatedBy Divya Koona. Added getCustomerById,getCustomerAddressById functions
 * @updatedBy Ranjith Rasakatla. Added single class for Payment
 */

namespace I95DevConnect\MessageQueue\Helper;

use \I95DevConnect\MessageQueue\Api\LoggerInterface;
use \Magento\Framework\App\Helper\Context;

/**
 * Generic helper class
 */
class Generic extends \Magento\Framework\App\Helper\AbstractHelper
{
    const SOURCE_ORDER_ID='source_order_id';

    /**
     * @var LoggerInterface
     */
    public $logger;

    /**
     * @var customSalesOrder
     */
    public $customSalesOrder;

    /**
     * @var customSalesShipment
     */
    public $customSalesShipment;

    /**
     * @var customSalesInvoice
     */
    public $customSalesInvoice;

    /**
     *
     * @var fileresolver
     */
    public $fileResolver;

    /**
     *
     * @var \Magento\Customer\Api\AddressRepositoryInterfaceFactory
     */
    public $addressRepository;

    /**
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterfaceFactory
     */
    public $customerRepository;

    /**
     *
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    public $searchCriteriaBuilder;

    public $customerAddressModel;

    /**
     * Generic constructor.
     * @param LoggerInterface $logger
     * @param \I95DevConnect\MessageQueue\Model\SalesOrder $customSalesOrder
     * @param \I95DevConnect\MessageQueue\Model\SalesShipment $customSalesShipment
     * @param \I95DevConnect\MessageQueue\Model\SalesInvoice $customInvoiceOrder
     * @param \Magento\Framework\Config\FileResolverInterface $fileResolver
     * @param \Magento\Customer\Api\AddressRepositoryInterfaceFactory $addressRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterfaceFactory $customerRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Customer\Model\AddressFactory $customerAddressModel
     * @param Context $context
     */
    public function __construct(
        LoggerInterface $logger,
        \I95DevConnect\MessageQueue\Model\SalesOrder $customSalesOrder,
        \I95DevConnect\MessageQueue\Model\SalesShipment $customSalesShipment,
        \I95DevConnect\MessageQueue\Model\SalesInvoice $customInvoiceOrder,
        \Magento\Framework\Config\FileResolverInterface $fileResolver,
        \Magento\Customer\Api\AddressRepositoryInterfaceFactory $addressRepository,
        \Magento\Customer\Api\CustomerRepositoryInterfaceFactory $customerRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Customer\Model\AddressFactory $customerAddressModel,
        Context $context
    ) {
        $this->logger = $logger;
        $this->customSalesOrder = $customSalesOrder;
        $this->customSalesShipment = $customSalesShipment;
        $this->customSalesInvoice = $customInvoiceOrder;
        $this->fileResolver = $fileResolver;
        $this->addressRepository = $addressRepository;
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerAddressModel = $customerAddressModel;
        parent::__construct($context);
    }

    /**
     * Getting supported product types for an order creation
     * @return array
     * @updatedBy Arushi Bansal
     */
    public function getSupportedProductTypesForOrder()
    {
        return $this->getSuportedProductTypes('Order');
    }

    /**
     * Getting supported product types for a product
     * @return array
     * @updatedBy Arushi Bansal
     */
    public function getSupportedTypesForProduct()
    {
        return $this->getSuportedProductTypes('Product');
    }

    /**
     * generic function for getting supported product types
     *
     * @param $entity
     *
     * @return array
     * @createdBy Arushi Bansal
     */
    public function getSuportedProductTypes($entity)
    {
        $supportedProductsArray = $supportedProductTypes = [];
        try {
            $xmlData = $this->fileResolver->get("settings.xml", 'global');

            if (count($xmlData) > 0) {
                foreach ($xmlData as $content) {
                    $xml = simplexml_load_string($content);
                    $currentEntity = json_decode(json_encode((array) $xml), 1);
                    if (isset($currentEntity['SupportedProductTypes'][$entity])) {
                        $supportedProductTypes[] = $currentEntity['SupportedProductTypes'][$entity];
                    }
                }
                $supportedProductTypes = implode(',', $supportedProductTypes);
            }
            $supportedProductsArray = explode(',', $supportedProductTypes);
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(
                __METHOD__,
                $ex->getMessage(),
                LoggerInterface::I95EXC,
                LoggerInterface::CRITICAL
            );
        }
        return $supportedProductsArray;
    }

    /**
     * Get source order id
     * @param string $targetOrderId
     * @return string
     */
    public function getSourceOrderId($targetOrderId)
    {
        $customOrder = $this->customSalesOrder
            ->getCollection()
            ->addFieldToSelect(self::SOURCE_ORDER_ID)
            ->addFieldToFilter('target_order_id', $targetOrderId);

        $customOrder->getSelect()->limit(1);
        $customOrder = $customOrder->getData();
        return isset($customOrder[0][self::SOURCE_ORDER_ID]) ? $customOrder[0][self::SOURCE_ORDER_ID] : '';
    }

    /**
     * Set target order status
     *
     * @param string $targetOrderId
     * @param string $targetOrderStatus
     *
     * @return boolean
     * @throws \Exception
     */
    public function setTargetOrderStatus($targetOrderId, $targetOrderStatus)
    {
        $customOrderModel = $this->customSalesOrder;
        $customOrderData = $customOrderModel->getCollection()
                        ->addFieldToSelect('id')
                        ->addFieldToFilter('target_order_id', $targetOrderId);

        $customOrderData->getSelect()->limit(1);
        $customOrderData = $customOrderData->getData();

        $id = isset($customOrderData[0]['id']) ? $customOrderData[0]['id'] : '';
        $customOrder = $customOrderModel->load($id);
        $customOrder->settargetOrderStatus($targetOrderStatus);
        $customOrder->save();
        return true;
    }

    /**
     * Get custom shipment id
     * @param string $sourceShipmentId
     * @return string
     */
    public function getCustomShipmentById($sourceShipmentId)
    {

        try {
            $shipment = '';
            $shipmentModel = $this->customSalesShipment;
            $shipmentData = $shipmentModel->getCollection()
                            ->addFieldToSelect('id')
                            ->addFieldToFilter('source_shipment_id', $sourceShipmentId);
            $shipmentData->getSelect()->limit(1);
            $shipmentData = $shipmentData->getData();

            $shipmentId = (isset($shipmentData[0]['id']) ? $shipmentData[0]['id'] : '');
            $shipment = $shipmentModel->load($shipmentId);
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(
                __METHOD__,
                $ex->getMessage(),
                LoggerInterface::I95EXC,
                LoggerInterface::CRITICAL
            );
        }

        return $shipment;
    }

    /**
     * Get custom invoice
     * @param string $sourceInvoiceId
     * @return string
     */
    public function getCustomInvoiceById($sourceInvoiceId)
    {

        try {
            $invoice = '';
            $invoiceModel = $this->customSalesInvoice;
            $invoiceData = $invoiceModel->getCollection()
                            ->addFieldToSelect('id')
                            ->addFieldToFilter('source_invoice_id', $sourceInvoiceId);
            $invoiceData->getSelect()->limit(1);
            $invoiceData = $invoiceData->getData();

            $invoiceId = (isset($invoiceData[0]['id']) ? $invoiceData[0]['id'] : '');
            $invoice = $invoiceModel->load($invoiceId);
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->createLog(__METHOD__, $ex->getMessage(), 'i95devException', 'critical');
        }

        return $invoice;
    }

    /**
     * Returns customer address based on address id
     *
     * @param int $addressId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @createdBy Divya Koona.
     */
    public function getCustomerAddressById($addressId)
    {
        try {
            $result = $this->addressRepository->create()->getById($addressId);
            return $result->__toArray();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__('i95dev_addr_020 %1', $addressId));
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException($ex->getMessage());
        }
    }

    /**
     * Fetch customer by customerId
     *
     * @param int $customerId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @createdBy Divya Koona.
     */
    public function getCustomerById($customerId)
    {
        try {
            $result = $this->customerRepository->create()->getById($customerId);
            return $result->__toArray();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__('i95dev_cust_012 %1', $customerId));
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException($ex->getMessage());
        }
    }

    /**
     * Get customer info by $targetCustomerId
     *
     * @param type $targetCustomerId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @updatedBy Divya Koona. Removed Magento REST API call.
     */
    public function getCustomerInfoByTargetId($targetCustomerId)
    {
        try {
            $searchCriteria = $this->searchCriteriaBuilder
                    ->addFilter('target_customer_id', $targetCustomerId, 'eq')
                    ->create();
            $searchResults = $this->customerRepository->create()->getList($searchCriteria);
            $customerInfo = $searchResults->getItems();
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(
                __METHOD__,
                $ex->getMessage(),
                LoggerInterface::I95EXC,
                LoggerInterface::CRITICAL
            );
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
        return $customerInfo;
    }

    /**
     * Retrieve address by target address id.
     *
     * @param string $targetBillingAddressId
     * @param $customerId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAddressByTargetAddressId($targetBillingAddressId, $customerId)
    {
        $customerAddress = $this->customerAddressModel->create();
        $adderssCollection = $customerAddress->getCollection()
            ->addFieldToSelect(['region_id', 'region', 'country_id'])
            ->addFieldToFilter("parent_id", $customerId)
            ->addFieldToFilter("target_address_id", $targetBillingAddressId);

        $adderssCollection->getSelect()->limit(1);

        if (empty($adderssCollection->getData()) && $adderssCollection->getSize() == 0) {
            throw new \Magento\Framework\Exception\LocalizedException(__('i95dev_addr_016'));
        } else {
            return $adderssCollection->getData()[0];
        }
    }
}
