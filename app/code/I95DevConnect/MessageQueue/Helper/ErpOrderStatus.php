<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Helper;

/**
 * Class to get ERP order status
 */
class ErpOrderStatus extends \Magento\Framework\App\Helper\AbstractHelper
{
    const PARENTITEM = "parent_item";
    const INTIAL = 'New';
    const COMPLETE = 'Complete';
    const INVOICED = 'Invoiced';
    const SHIPPED = 'Shipped';
    const PARTIALINVOICE = 'Partially Invoiced';
    const PARTIALSHIP = 'Partially Shipped';
    const CANCEL = 'Canceled';

    /**
     * @var Data
     */
    public $baseHelperData;

    /**
     * @var \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory
     */
    public $logger;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Model\SalesInvoiceFactory
     */
    public $customInvoiceFactory;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Model\SalesShipmentFactory
     */
    public $customShipment;

    /**
     *
     * @var \Magento\Sales\Model\Order\ItemFactory
     */
    public $orderItem;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $data;

    /**
     * @var \Magento\Sales\Api\Data\TransactionSearchResultInterfaceFactory
     */
    public $transactionsFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterfaceFactory
     */
    public $orderRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    public $searchCriteria;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroupFactory
     */
    public $filterGroup;

    /**
     * @var \Magento\Framework\Api\FilterFactory
     */
    public $filter;

    /**
     * @var \Magento\Sales\Api\InvoiceRepositoryInterfaceFactory
     */
    public $invoiceRepository;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterfaceFactory
     */
    public $productRepository;

    /**
     * @var \I95DevConnect\MessageQueue\Model\SalesOrderFactory
     */
    public $customSalesOrder;

    /**
     * ErpOrderStatus constructor.
     *
     * @param \I95DevConnect\MessageQueue\Model\SalesInvoiceFactory $customInvoiceFactory
     * @param \I95DevConnect\MessageQueue\Model\SalesShipmentFactory $customShipment
     * @param \Magento\Sales\Model\Order\ItemFactory $orderItem
     * @param \Magento\Sales\Api\Data\TransactionSearchResultInterfaceFactory $transactionsFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterfaceFactory $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param \Magento\Framework\Api\Search\FilterGroupFactory $filterGroup
     * @param \Magento\Framework\Api\FilterFactory $filter
     * @param \Magento\Sales\Api\InvoiceRepositoryInterfaceFactory $invoiceRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepository
     * @param \I95DevConnect\MessageQueue\Model\SalesOrderFactory $customSalesOrder
     * @param Data $baseHelperData
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Model\SalesInvoiceFactory $customInvoiceFactory,
        \I95DevConnect\MessageQueue\Model\SalesShipmentFactory $customShipment,
        \Magento\Sales\Model\Order\ItemFactory $orderItem,
        \Magento\Sales\Api\Data\TransactionSearchResultInterfaceFactory $transactionsFactory,
        \Magento\Sales\Api\OrderRepositoryInterfaceFactory $orderRepository,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        \Magento\Framework\Api\Search\FilterGroupFactory $filterGroup,
        \Magento\Framework\Api\FilterFactory $filter,
        \Magento\Sales\Api\InvoiceRepositoryInterfaceFactory $invoiceRepository,
        \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepository,
        \I95DevConnect\MessageQueue\Model\SalesOrderFactory $customSalesOrder,
        \I95DevConnect\MessageQueue\Helper\Data $baseHelperData,
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->logger = $logger;
        $this->customInvoiceFactory = $customInvoiceFactory;
        $this->customShipment = $customShipment;
        $this->orderItem = $orderItem;
        $this->transactionsFactory = $transactionsFactory;
        $this->orderRepository = $orderRepository;
        $this->searchCriteria = $searchCriteria;
        $this->filterGroup = $filterGroup;
        $this->filter = $filter;
        $this->invoiceRepository = $invoiceRepository;
        $this->productRepository = $productRepository;
        $this->customSalesOrder = $customSalesOrder;
        $this->baseHelperData = $baseHelperData;
        parent::__construct($context);
    }

    /**
     * Get parent of simple products
     * @param obj $order
     * @return array
     */
    public function getParentSimpleItems($order)
    {
        $parentSimpleItems = [];
        foreach ($order['items'] as $item) {
            if (isset($item[self::PARENTITEM])) {
                $parentSimpleItems[] = $item[self::PARENTITEM]['item_id'];
            }
        }
        return $parentSimpleItems;
    }

    /**
     * get ERP order status
     * @param string $orderId
     * @return string
     */
    public function getERPOrderStatus($orderId)
    {
        $status = [];
        $shippedOrderedQty = 0;
        $invoiceOrderedQty = 0;
        $cancelOrderedQty = 0;
        $shippedQty=0;
        $order = $this->getOrder($orderId);
        $parentSimpleItems = $this->getParentSimpleItems($order);
        $invoices = $this->getInvoices($order->getEntityId());
        $targetInvoicedQty = $this->getTargetInvoicedQty($invoices);
        foreach ($order->getItems() as $item) {
            $nonShippedTypes = ['virtual', 'downloadable'];
            if (!in_array($item->getProductType(), $nonShippedTypes) &&
                !in_array($item->getItemId(), $parentSimpleItems)) {
                $shippedOrderedQty += (int)$item->getQtyOrdered();
                if ($item->getParentItem()) {
                    $shippedQty += (int)$item[self::PARENTITEM]['qty_shipped'];
                } else {
                    $shippedQty += (int)$item->getQtyShipped();
                }
            }

            if (!in_array($item->getItemId(), $parentSimpleItems)) {
                $invoiceOrderedQty += (int)$item->getQtyOrdered();
            }

            if (!in_array($item->getItemId(), $parentSimpleItems)) {
                $cancelOrderedQty += (int)$item->getQtyCanceled();
            }
        }
        $invoicedQty = (int)$targetInvoicedQty;

        $status = $this->generateStatus(
            $shippedOrderedQty,
            $shippedQty,
            $invoiceOrderedQty,
            $invoicedQty,
            $cancelOrderedQty
        );

        return implode(',', $status);
    }

    /**
     * @param $shippedOrderedQty
     * @param $shippedQty
     * @param $invoiceOrderedQty
     * @param $invoicedQty
     * @param $cancelOrderedQty
     * @return string
     */
    public function generateStatus($shippedOrderedQty, $shippedQty, $invoiceOrderedQty, $invoicedQty, $cancelOrderedQty)
    {
        $status = $this->setFullOrderStatus($shippedOrderedQty, $shippedQty, $invoiceOrderedQty, $invoicedQty);
        if ($cancelOrderedQty !== 0) {
            $status = $this->setCancelOrderStatus(
                $shippedOrderedQty,
                $shippedQty,
                $invoiceOrderedQty,
                $invoicedQty,
                $cancelOrderedQty
            );
        }
        if ($shippedOrderedQty != $shippedQty && $invoiceOrderedQty == $invoicedQty) {
            $status[] = self::INVOICED;
            if ($shippedQty !== 0) {
                $status[] = self::PARTIALSHIP;
            }
        }
        return $this->setPartialOrderStatus(
            $status,
            $shippedOrderedQty,
            $shippedQty,
            $invoiceOrderedQty,
            $invoicedQty,
            $cancelOrderedQty
        );
    }

    /**
     * set partial status to order
     * @param $status
     * @param $shippedOrderedQty
     * @param $shippedQty
     * @param $invoiceOrderedQty
     * @param $invoicedQty
     * @param $cancelOrderedQty
     * @return mixed
     */
    public function setPartialOrderStatus(
        $status,
        $shippedOrderedQty,
        $shippedQty,
        $invoiceOrderedQty,
        $invoicedQty,
        $cancelOrderedQty
    ) {
        if ($shippedOrderedQty != $shippedQty && $invoiceOrderedQty != $invoicedQty) {
            if ($shippedQty !== 0 && $cancelOrderedQty === 0) {
                $status[] = self::PARTIALSHIP;
            }
            if ($invoicedQty !== 0 && $cancelOrderedQty === 0) {
                $status[] = self::PARTIALINVOICE;
            }
            if ($shippedQty === 0 && $invoicedQty === 0 && $cancelOrderedQty === 0) {
                $status[] = self::INTIAL;
            }
        }
        return $status;
    }

    /**
     * @param $shippedOrderedQty
     * @param $shippedQty
     * @param $invoiceOrderedQty
     * @param $invoicedQty
     * @return string
     */
    public function setFullOrderStatus($shippedOrderedQty, $shippedQty, $invoiceOrderedQty, $invoicedQty)
    {
        $status = [];
        if ($shippedOrderedQty == $shippedQty && $invoiceOrderedQty == $invoicedQty) {
            $status[] = self::COMPLETE;
        }

        if ($shippedOrderedQty == $shippedQty && $invoiceOrderedQty != $invoicedQty) {
            $status[] = self::SHIPPED;
            if ($invoicedQty !== 0) {
                $status[] = self::PARTIALINVOICE;
            }
        }

        return $status;
    }

    /**
     * @param $shippedOrderedQty
     * @param $shippedQty
     * @param $invoiceOrderedQty
     * @param $invoicedQty
     * @param $cancelOrderedQty
     * @return string
     */
    public function setCancelOrderStatus(
        $shippedOrderedQty,
        $shippedQty,
        $invoiceOrderedQty,
        $invoicedQty,
        $cancelOrderedQty
    ) {
        $status = [];
        if ($invoiceOrderedQty == $cancelOrderedQty) {
            $status[] = self::CANCEL;
        }

        if ($shippedQty != 0 && $invoicedQty != 0 &&
            $shippedOrderedQty == $shippedQty+$cancelOrderedQty &&
            $invoiceOrderedQty == $invoicedQty+$cancelOrderedQty) {
            $status[] = self::COMPLETE;
        }

        return $status;
    }

    /**
     * Get target invoices
     * @param obj $invoices
     * @return int
     */
    public function getTargetInvoicedQty($invoices)
    {
        $targetInvoicedQty = 0;
        if (!empty($invoices)) {
            foreach ($invoices as $inv) {
                $sourceInvoiceId = $inv->getIncrementId();
                $customInvoice = $this->customInvoiceFactory->create()->getCollection()
                    ->addFieldToSelect('target_invoiced_qty')
                    ->addFieldToFilter('source_invoice_id', $sourceInvoiceId);
                $customInvoice->getSelect()->limit(1);

                $customInvoice = $customInvoice->getFirstItem();
                $targetInvoicedQty += $customInvoice->getTargetInvoicedQty();
            }
        }

        return $targetInvoicedQty;
    }

    /**
     * Get invoices
     *
     * @param string $orderId
     *
     * @return \Magento\Sales\Api\Data\InvoiceSearchResultInterface|null
     */
    public function getInvoices($orderId)
    {
        $filter[0] = $this->filter->create()->setField('order_id')->setValue($orderId)->setConditionType('eq');
        $filterGroup[0] = $this->filterGroup->create()->setFilters($filter);
        $this->searchCriteria->setFilterGroups($filterGroup);
        $invoices = $this->invoiceRepository->create()->getList($this->searchCriteria);

        if ($invoices->getSize() > 0) {
            return $invoices;
        } else {
            return null;
        }
    }

    /**
     * Get Order data
     * @param string $orderId
     * @return \Magento\Sales\Api\Data\OrderInterface|null
     */
    public function getOrder($orderId)
    {
        $result = $this->orderRepository->create()->get($orderId);

        if ($result instanceof \Magento\Sales\Api\Data\OrderInterface) {
            return $result;
        } else {
            return null;
        }
    }

    /**
     * Fetch product by product sku
     *
     * @param string $sku
     * @return \Magento\Catalog\Api\Data\ProductInterface|null
     */
    public function getProduct($sku)
    {
        $filter[0] = $this->filter->create()->setField('sku')->setValue($sku)->setConditionType('eq');
        $filterGroup[0] = $this->filterGroup->create()->setFilters($filter);
        $this->searchCriteria->setFilterGroups($filterGroup);
        $products = $this->productRepository->create()->getList($this->searchCriteria);

        if ($products->getTotalCount() > 0) {
            foreach ($products->getItems() as $product) {
                return $product;
            }
        }
        return null;
    }

    /**
     * Get order data by order id
     *
     * @param string $orderId
     * @return array
     */
    public function getOrderByIncrementId($orderId)
    {
        $filter[0] = $this->filter->create()->setField('increment_id')->setValue($orderId)->setConditionType('eq');
        $filterGroup[0] = $this->filterGroup->create()->setFilters($filter);
        $this->searchCriteria->setFilterGroups($filterGroup);
        $orders = $this->orderRepository->create()->getList($this->searchCriteria);

        if ($orders->getSize() > 0) {
            foreach ($orders as $order) {
                return $order;
            }
        }
        return null;
    }

    /**
     * Checking order is able to sync or not
     * @param string $orderId
     * @return string
     * @author Sravani Polu
     * Removed API call and used existing interface call to get order info.
     */
    public function isOrderSyncable($orderId)
    {
        $order = $this->getOrderByIncrementId($orderId);
        $message = '';
        if ($order !== null) {
            if ($order->getStatus() == "canceled") {
                $message = "Order with Cancel status are not trasnfered";
            }

            if ($order->getStatus() == "fraud") {
                $message = "Order with Fraud status are not trasnfered";
            }

            if ($order->getPayment()->getMethod() == "authorizenet_directpost") {
                $transactions = $this->transactionsFactory->create()->addOrderIdFilter(
                    $order->getEntityId()
                )->getItems();
                if (empty($transactions)) {
                    $message = "There was some issue with authorize.net (No transaction id exists)";
                }
            }
        }
        if ($message !== '') {
            return $message;
        }
        return null;
    }

    /**
     * Update i95dev order status during invoice sync
     *
     * @param $customSalesOrderId
     * @param $entityId
     * @throws \Exception
     * @createdBy Arushi Bansal
     */
    public function updateCustomOrderStatus($customSalesOrderId, $entityId)
    {
        try {
            // Update i95dev order status during invoice sync
            $customOrderModel = $this->customSalesOrder->create()->load(
                $customSalesOrderId,
                "source_order_id"
            );
            $customOrderModel->setUpdatedDt($this->baseHelperData->date->gmtDate());
            $targetOrderStatus = $this->getERPOrderStatus(
                $entityId
            );
            $customOrderModel->setTargetOrderStatus($targetOrderStatus);
            $customOrderModel->save();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->create()->createLog(
                __METHOD__,
                $e->getMessage(),
                \I95DevConnect\MessageQueue\Api\LoggerInterface::INFO,
                'info'
            );
        }
    }
}
