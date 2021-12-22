<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Block\Adminhtml\Order\View;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use \Magento\Store\Model\ScopeInterface;

/**
 * Block for displaying target information in order view page
 * @api
 */
class Info extends \Magento\Backend\Block\Template
{

    const PAYMENTMETHOD = 'checkmo';
    const TARGET_ORDER_ID = 'target_order_id';

    protected $_template = 'I95DevConnect_MessageQueue::order/view/custom_info.phtml';

    /**
     * @var customSalesOrder
     */
    public $customSalesOrder;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $basedata;

    /**
     * @var \Magento\Sales\Model\Order
     */
    public $salesOrderModel;

    /**
     * @var generic
     */
    public $generic;

    /**
     * @var I95DevConnect\MessageQueue\Model\ChequeNumber
     */
    public $chequeNumberModel;

    /**
     *
     * Info constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param \I95DevConnect\MessageQueue\Model\SalesOrder $customSalesOrder
     * @param \I95DevConnect\MessageQueue\Helper\Data $helper
     * @param \Magento\Sales\Model\Order $salesOrderModel
     * @param \I95DevConnect\MessageQueue\Helper\Generic $generic
     * @param \I95DevConnect\MessageQueue\Model\ChequeNumber $chequeNumber
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \I95DevConnect\MessageQueue\Model\SalesOrder $customSalesOrder,
        \I95DevConnect\MessageQueue\Helper\Data $helper,
        \Magento\Sales\Model\Order $salesOrderModel,
        \I95DevConnect\MessageQueue\Helper\Generic $generic,
        \I95DevConnect\MessageQueue\Model\ChequeNumber $chequeNumber,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->customSalesOrder = $customSalesOrder;
        $this->basedata = $helper;
        $this->salesOrderModel = $salesOrderModel;
        $this->generic = $generic;
        $this->storeManager = $storeManager;
        $this->chequeNumberModel = $chequeNumber;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve Order id
     *
     * @return string|null
     */
    public function getOrderId()
    {
        return $this->coreRegistry->registry('current_order');
    }

    /**
     * Get target order id
     * @return string
     */
    public function getTargetOrderId()
    {
        $order = $this->getOrderId();
        $sourceOrderId = $order->getIncrementId();
        $customCollection = $this->customSalesOrder
            ->getCollection();
        $customCollection->addFieldToSelect([self::TARGET_ORDER_ID, 'target_order_status'])
            ->addFieldToFilter('source_order_id', $sourceOrderId);

        $customCollection->getSelect()->limit(1);

        return $customCollection->getData();
    }

    /**
     * Retrieves target invoice id
     * @return array
     */
    public function getTargetInvoiceId()
    {
        try {
            $targetInvoiceId = [];
            $order = $this->getOrderId();
            $invoices = $order->getInvoiceCollection()->getData();
            foreach ($invoices as $invoice):
                $sourceInvoiceId = $invoice['increment_id'];
                $customInvoice = $this->generic->getCustomInvoiceById($sourceInvoiceId);
                $targetInvoiceId[] = $customInvoice->gettargetInvoiceId(); //changed
            endforeach;
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->basedata->createLog(__METHOD__, $ex->getMessage(), "i95devException", 'critical');
        }
        return $targetInvoiceId;
    }

    /**
     * Retrieves target invoice id
     * @return array
     */
    public function getTargetShipmentId()
    {
        try {
            $targetShipmentId = [];

            $order = $this->getOrderId();
            $shipments = $order->getShipmentsCollection()->getData();
            foreach ($shipments as $shipment):
                $sourceShipmentId = $shipment['increment_id'];
                $cutomshipment = $this->generic->getCustomShipmentById($sourceShipmentId);
                $targetShipmentId[] = $cutomshipment->gettargetShipmentId(); //changed
            endforeach;
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->basedata->criticalLog(__METHOD__, $ex->getMessage(), "i95devException");
        }
        return $targetShipmentId;
    }

    /**
     * To get Component
     * @return string
     */
    public function getComponent()
    {
        $componentPath = 'i95dev_messagequeue/I95DevConnect_settings/component';
        return $this->basedata->getscopeConfig(
            $componentPath,
            ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );
    }

    /**
     * To get custom attribute
     * @return string
     */
    public function getCustomAttribute()
    {
        $targetOrder = $this->getTargetOrderId();
        $msg = 'Order Sync In Process';
        return isset($targetOrder[0][self::TARGET_ORDER_ID]) ? $targetOrder[0][self::TARGET_ORDER_ID] : $msg;
    }

    /**
     * Checks custom attribute
     * @return boolean
     */
    public function checkCustomAttribute()
    {
        $targetOrder = $this->getTargetOrderId();
        $targetOrderId = isset($targetOrder[0][self::TARGET_ORDER_ID]) ? $targetOrder[0][self::TARGET_ORDER_ID] : '';
        $origin = isset($targetOrder[0]['origin']) ? $targetOrder[0]['origin'] : '';
        if ($targetOrderId == "" && $origin === null) {
            return false;
        }
        return true;
    }

    /**
     * check if module is enabled
     * @return boolean
     */
    public function isI95DevConnectEnabled()
    {
        return $this->basedata->isEnabled();
    }
}
