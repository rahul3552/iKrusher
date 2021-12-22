<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Block\Adminhtml\Invoice\View;

/**
 * Block to retrieve Invoice info
 * @api
 */
class Info extends \Magento\Backend\Block\Template
{
    const I95EXC = 'i95devApiException';

    /**
     * @var customSalesInvoice
     */
    public $customSalesInvoice;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $basedata;
    /**
     * @var salesShipment
     */
    public $salesShipment;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Api\LoggerInterface
     */
    public $logger;

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $mqHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \I95DevConnect\MessageQueue\Model\SalesInvoice $customSalesInvoice
     * @param \Magento\Sales\Model\Order\Shipment $salesShipment
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterface $logger
     * @param \I95DevConnect\MessageQueue\Helper\Data $mqHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \I95DevConnect\MessageQueue\Model\SalesInvoice $customSalesInvoice,
        \Magento\Sales\Model\Order\Shipment $salesShipment,
        \I95DevConnect\MessageQueue\Api\LoggerInterface $logger,
        \I95DevConnect\MessageQueue\Helper\Data $mqHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->customSalesInvoice = $customSalesInvoice;
        $this->salesShipment = $salesShipment;
        $this->logger = $logger;
        $this->mqHelper = $mqHelper;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve current invoice
     * @return string|null
     */
    public function getCurrentInvoice()
    {
        return $this->coreRegistry->registry('current_invoice');
    }

    /**
     * Retrive invoice information from custom collection
     * @return \I95DevConnect\MessageQueue\Model\SalesInvoice $customSalesInvoice
     */
    public function getCustomInvoice()
    {
        $invoice = $this->getCurrentInvoice();
        $sourceInvoiceId = $invoice->getIncrementId();
        $customSalesInvoiceColl = $this->customSalesInvoice->getCollection();

        $customSalesInvoiceColl->addFieldToFilter('source_invoice_id', $sourceInvoiceId);

        $customSalesInvoiceColl->getSelect()->limit(1);
        
        return $customSalesInvoiceColl->getData();
    }

    /**
     * Retrieves AX Payment Journal Ids from Invoice
     * @return string
     */
    public function getTargetPaymentIds()
    {
        $targetPaymentIds = '';
        try {
            $customInvoice = $this->getCustomInvoice();
                      
            if (isset($customInvoice[0]) && isset($customInvoice[0]['cash_receipt_number'])) {
                $targetPaymentIds = $customInvoice[0]['cash_receipt_number'];
            }

        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(__METHOD__, $ex->getMessage(), self::I95EXC, 'critical');
        }
        return $targetPaymentIds;
    }

    /**
     * To get Current Component
     * @return string
     */
    public function getComponent()
    {
        return $this->mqHelper->getscopeConfig(
            'i95dev_messagequeue/I95DevConnect_settings/component',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );
    }
    /**
     * To check module is enable/disable
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->mqHelper->isEnabled();
    }
    /**
     * Retrieves target invoice id
     * @return string
     */
    public function getTargetInvoiceId()
    {
        $targetInvoiceId = '';
        try {
            $customInvoice = $this->getCustomInvoice();
                      
            if (isset($customInvoice[0]) && isset($customInvoice[0]['target_invoice_id'])) {
                $targetInvoiceId = $customInvoice[0]['target_invoice_id'];
            }

        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(__METHOD__, $ex->getMessage(), self::I95EXC, 'critical');
        }
        return $targetInvoiceId;
    }
}
