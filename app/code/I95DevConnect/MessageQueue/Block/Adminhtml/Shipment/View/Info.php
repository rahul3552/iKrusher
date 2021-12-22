<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Block\Adminhtml\Shipment\View;

/**
 * Block for displaying target information in shipment view page
 * @api
 */
class Info extends \Magento\Backend\Block\Template
{

    const TARGET_SHIPMENT_ID = 'target_shipment_id';

    /**
     * @var customSalesShipment
     */
    public $customSalesShipment;

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
     * @var salesInvoice
     */
    public $salesInvoice;

    /**
     * @var \I95DevConnect\MessageQueue\Api\LoggerInterface
     */
    public $logger;

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $mqHelper;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \I95DevConnect\MessageQueue\Model\SalesShipment $customSalesShipment
     * @param \Magento\Sales\Model\Order\Invoice $salesInvoice
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterface $logger
     * @param \I95DevConnect\MessageQueue\Helper\Data $mqHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \I95DevConnect\MessageQueue\Model\SalesShipment $customSalesShipment,
        \Magento\Sales\Model\Order\Invoice $salesInvoice,
        \I95DevConnect\MessageQueue\Api\LoggerInterface $logger,
        \I95DevConnect\MessageQueue\Helper\Data $mqHelper,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->customSalesShipment = $customSalesShipment;
        $this->salesInvoice = $salesInvoice;
        $this->logger = $logger;
        $this->mqHelper = $mqHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve current shipment
     * @return string|null
     */
    public function getCurrentShipment()
    {
        return $this->coreRegistry->registry('current_shipment');
    }

    /**
     * Retrieve shipment information from custom collection
     * @return string
     */
    public function getCustomShipment()
    {

        $shipment = $this->getCurrentShipment();
        $sourceShipmentId = $shipment->getIncrementId();
        $this->customSalesShipment = $this->customSalesShipment->getCollection();
        $this->customSalesShipment->addFieldToSelect(self::TARGET_SHIPMENT_ID)
            ->addFieldToFilter('source_shipment_id', $sourceShipmentId);

        $this->customSalesShipment->getSelect()->limit(1);

        return $this->customSalesShipment->getData();
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
    public function getTargetShipmentId()
    {
        $targetShipmentId = '';
        try {

            $customShipment = $this->getCustomShipment();

            if (isset($customShipment[0]) && isset($customShipment[0][self::TARGET_SHIPMENT_ID])) {
                $targetShipmentId = $customShipment[0][self::TARGET_SHIPMENT_ID];
            }

        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->mqHelper->criticalLog(__METHOD__, $ex->getMessage(), "i95devException");
        }
        return $targetShipmentId;
    }
}
