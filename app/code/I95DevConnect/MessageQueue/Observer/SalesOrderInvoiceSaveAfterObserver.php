<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 * @updatedBy Divya Koona. Removed getCustomOrder() function as it is not used.
 */

namespace I95DevConnect\MessageQueue\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\Http;

/**
 * Observer class for sales order invoice after save
 */
class SalesOrderInvoiceSaveAfterObserver implements ObserverInterface
{

    const MAGLOGNAME = 'MagentoToERP';
    const ERPLOGNAME = 'ERPToMagento';
    const I95EXC = 'i95devApiException';
    const PAYMENTMETHOD = 'checkmo';

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $data;

    /**
     * @var \Magento\Sales\Model\Order
     */
    public $salesOrderModel;

    /**
     * @var customSalesOrder
     */
    public $customSalesInvoice;

    /**
     * @var customSalesOrder
     */
    public $customSalesOrder;

    public $request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    public $logger;

    /**
     * SalesOrderInvoiceSaveAfterObserver constructor.
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $data
     * @param \Magento\Sales\Model\Order $salesOrderModel
     * @param \I95DevConnect\MessageQueue\Model\SalesInvoiceFactory $customInvoiceOrder
     * @param \I95DevConnect\MessageQueue\Model\SalesOrderFactory $customSalesOrder
     * @param Http $request
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $data,
        \Magento\Sales\Model\Order $salesOrderModel,
        \I95DevConnect\MessageQueue\Model\SalesInvoiceFactory $customInvoiceOrder,
        \I95DevConnect\MessageQueue\Model\SalesOrderFactory $customSalesOrder,
        Http $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {

        $this->data = $data;
        $this->salesOrderModel = $salesOrderModel;
        $this->customSalesInvoice = $customInvoiceOrder;
        $this->customSalesOrder = $customSalesOrder;
        $this->request = $request;
        $this->storeManager = $storeManager;
    }

    /**
     * Save custom invoice
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @throws \Exception
     * @updatedBy Divya Koona. Removed gp_orderprocess_flag related code.
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $is_enabled = $this->data->isEnabled();
        if (!$is_enabled) {
            return;
        }
        if ($this->data->getGlobalValue('i95_observer_skip') || $this->request->getParam('isI95DevRestReq') == 'true') {
            return;
        }
        try {
            $invoice = $observer->getEvent()->getInvoice();
            $orderId = $invoice->getOrderId();
            $isTotalQtyInvoiced = $this->isTotalOrderQtyInvoiced($orderId);
            if ($isTotalQtyInvoiced) {
                $this->createCustomSalesInvoice($invoice);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(__METHOD__, $ex->getMessage(), self::I95EXC, 'critical');
        }
    }

    /**
     * create invoice entry in custom tables of i95dev
     *
     * @param Object $invoice
     * @param int $qty
     *
     * @author Arushi Bansal
     */
    public function createCustomSalesInvoice($invoice, int $qty = null)
    {
        $invoiceModel = $this->customSalesInvoice->create();
        $loadCustomInvoice = $this->customSalesInvoice->create()
                ->load($invoice->getIncrementId(), 'source_invoice_id');
        if ($loadCustomInvoice->getId()) {
            $invoiceModel->setId($loadCustomInvoice->getId());
        }
        if ($qty !== 0) {
            $invoiceModel->setTargetInvoicedQty($qty);
        }
        $invoiceModel->setSourceInvoiceId($invoice->getIncrementId());
        $invoiceModel->setCreatedDt($invoice->getCreatedAt());
        $invoiceModel->setUpdatedDt($invoice->getUpdatedAt());
        $invoiceModel->setUpdateBy('Magento');
        $invoiceModel->save();
    }
    /**
     * Check all items qty invoiced or not for order
     * @param  int $orderId
     * @return boolean
     */
    public function isTotalOrderQtyInvoiced($orderId)
    {
        $is_enabled = $this->data->isEnabled();
        if (!$is_enabled) {
            return false;
        }
        $order = $this->salesOrderModel->load($orderId);
        $orderedQty = $order->getData('total_qty_ordered');
        $totalQty = 0;
        foreach ($order->getInvoiceCollection() as $invoice) {
            $qtyInvoiced = $invoice->getData('total_qty');
            $totalQty = $totalQty + $qtyInvoiced;
        }
        $nonInvoiceTypes = ['configurable'];
        $nonInvoicedQtyArray = [];

        /* get parent orderQty */
        foreach ($order->getAllItems() as $orderedItem) {
            if (in_array($orderedItem->getProductType(), $nonInvoiceTypes)) {
                $nonInvoicedQtyArray[] = $orderedItem->getQtyOrdered();
            }
        }
        $nonInvoicedQty = array_sum($nonInvoicedQtyArray);
        $totalQty = $totalQty - $nonInvoicedQty;
        $orderedQty = (int) $orderedQty;
        if ($orderedQty == $totalQty) {
            return true;
        }
        return false;
    }
}
