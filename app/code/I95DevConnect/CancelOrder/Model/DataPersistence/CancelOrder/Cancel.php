<?php

/**
 * @author    i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package   I95DevConnect_CancelOrder
 */

namespace I95DevConnect\CancelOrder\Model\DataPersistence\CancelOrder;

use \I95DevConnect\MessageQueue\Model\AbstractDataPersistence;
use \I95DevConnect\MessageQueue\Api\LoggerInterface;
use \Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class for Cancel order
 */
class Cancel extends AbstractDataPersistence
{
    const ITEMS='items';
    const CUSTOMER='customer';
    const CRITICAL='critical';
    const STATUS='status';
    const TARGETID='targetId';
    const CREDITMEMOID='creditmemoId';
    const MESSAGE='message';
    const I95OSKIP='i95_observer_skip';
    const TRANSTYPE = 'transaction_type';
    public $errorMsg = 'Something went wrong. Please contact admin.';

    /**
     * @var \Magento\Sales\Model\Order
     */
    public $orderModel;
    public $customSalesOrder;

    /**
     * @var \Magento\Sales\Api\OrderManagementInterface
     */
    public $orderManagement;

    /**
     * @var \Magento\Sales\Model\Service\CreditmemoService
     */
    public $creditmemoService;

    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader
     */
    public $creditmemoLoader;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\OrderCommentSender
     */
    public $commentEmailSender;
    public $messagequeueData;
    public $messageQueueFactory;
    public $orderRepository;
    public $customerRepository;
    public $searchCriteriaBuilder;
    public $customerRepositoryInterfaceFactory;
    public $creditmemoFactory;
    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $dataHelper;
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    public $orderFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;
    /**
     * @var \I95DevConnect\CancelOrder\Helper\Data
     */
    public $data;

    /**
     * @var generic
     */
    public $generic;

    /**
     * @var \I95DevConnect\MessageQueue\Model\ResourceModel\I95DevInvoiceHistory\CollectionFactory
     */
    protected $invoiceHistory;
    public $erpOrderStatus;

    /**
     * Cancel constructor.
     * @param \Magento\Sales\Model\OrderFactory $orderModel
     * @param \I95DevConnect\MessageQueue\Model\SalesOrderFactory $customSalesOrder
     * @param \Magento\Sales\Api\OrderManagementInterface $orderManagement
     * @param \Magento\Sales\Model\Service\CreditmemoService $creditmemoService
     * @param \Magento\Sales\Model\Order\Email\Sender\OrderCommentSender $commentEmailSender
     * @param \Magento\Framework\Json\Decoder $jsonDecoder
     * @param \I95DevConnect\MessageQueue\Api\I95DevResponseInterfaceFactory $i95DevResponse
     * @param \I95DevConnect\MessageQueue\Model\ErrorUpdateDataFactory $messageErrorModel
     * @param \I95DevConnect\MessageQueue\Api\Data\I95DevErpMQInterfaceFactory $i95DevErpMQ
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param \I95DevConnect\MessageQueue\Api\I95DevErpMQRepositoryInterfaceFactory $i95DevErpMQRepository
     * @param DateTime $date
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
     * @param \I95DevConnect\MessageQueue\Api\I95DevErpDataRepositoryInterfaceFactory $i95DevERPDataRepository
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \I95DevConnect\CancelOrder\Helper\Data $data
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Customer\Api\CustomerRepositoryInterfaceFactory $customerRepositoryInterfaceFactory
     * @param \I95DevConnect\MessageQueue\Helper\Generic $generic
     * @param \I95DevConnect\MessageQueue\Model\ResourceModel\I95DevInvoiceHistory\CollectionFactory $invoiceHistory
     * @param \I95DevConnect\MessageQueue\Helper\ErpOrderStatus $erpOrderStatus
     */
    public function __construct( // NOSONAR
        \Magento\Sales\Model\OrderFactory $orderModel,
        \I95DevConnect\MessageQueue\Model\SalesOrderFactory $customSalesOrder,
        \Magento\Sales\Api\OrderManagementInterface $orderManagement,
        \Magento\Sales\Model\Service\CreditmemoService $creditmemoService,
        \Magento\Sales\Model\Order\Email\Sender\OrderCommentSender $commentEmailSender,
        \Magento\Framework\Json\Decoder $jsonDecoder,
        \I95DevConnect\MessageQueue\Api\I95DevResponseInterfaceFactory $i95DevResponse,
        \I95DevConnect\MessageQueue\Model\ErrorUpdateDataFactory $messageErrorModel,
        \I95DevConnect\MessageQueue\Api\Data\I95DevErpMQInterfaceFactory $i95DevErpMQ,
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \I95DevConnect\MessageQueue\Api\I95DevErpMQRepositoryInterfaceFactory $i95DevErpMQRepository,
        DateTime $date,
        \Magento\Framework\Event\Manager $eventManager,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate,
        \I95DevConnect\MessageQueue\Api\I95DevErpDataRepositoryInterfaceFactory $i95DevERPDataRepository,
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \I95DevConnect\CancelOrder\Helper\Data $data,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Customer\Api\CustomerRepositoryInterfaceFactory $customerRepositoryInterfaceFactory,
        \I95DevConnect\MessageQueue\Helper\Generic $generic,
        \I95DevConnect\MessageQueue\Model\ResourceModel\I95DevInvoiceHistory\CollectionFactory $invoiceHistory,
        \I95DevConnect\MessageQueue\Helper\ErpOrderStatus $erpOrderStatus
    ) {
        $this->orderFactory = $orderModel;
        $this->customSalesOrder = $customSalesOrder;
        $this->orderManagement = $orderManagement;
        $this->creditmemoService = $creditmemoService;
        $this->commentEmailSender = $commentEmailSender;
        $this->creditmemoFactory = $creditmemoFactory;
        $this->dataHelper = $dataHelper;
        $this->storeManager = $storeManager;
        $this->orderRepository = $orderRepository;
        $this->data = $data;
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerRepositoryInterfaceFactory = $customerRepositoryInterfaceFactory;
        $this->generic = $generic;
        $this->invoiceHistory = $invoiceHistory;
        $this->erpOrderStatus = $erpOrderStatus;
        parent::__construct(
            $jsonDecoder,
            $i95DevResponse,
            $messageErrorModel,
            $i95DevErpMQ,
            $logger,
            $i95DevErpMQRepository,
            $date,
            $eventManager,
            $validate,
            $i95DevERPDataRepository
        );
    }

    /**
     * cancel order
     *
     * @param  data object
     * @return $this
     * @throws \Exception
     */
    public function cancelOrder($stringData)
    {
        if (!$this->data->isEnabled()) {
            return $this->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                __("I95DevConnect Cancel Order extension is currently disabled. Enable the extension to proceed sync."),
                null
            );
        }
        try {
            $this->setStringData($stringData);
            $this->validate();
            $this->orderId = $this->order->getEntityId();
            // @updatedBy Subhan. Removed orderstatus flag and added if auth_capture
            $paymentDetails = $this->order->getPayment();
            $transactionDetails = $paymentDetails->getAdditionalInformation();
            $transactionType = '';
            if (isset($transactionDetails[self::TRANSTYPE])) {
                $transactionType = $transactionDetails[self::TRANSTYPE];
            }
            if (strtolower($transactionType) === 'auth_capture') {
                $creditMemoId = $this->processProcessingStatusWithInvoice();
            } elseif (strtolower($paymentDetails->getMethod()) === 'paypal_express'
                && $this->order->hasInvoices()) {
                $creditMemoId = $this->processProcessingStatusWithInvoice();
            } else {
                $result = $this->cancelMagentoOrder();
                if (!$result) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __($this->errorMsg)
                    );
                }
                //@hrusikesh Updating Customer Credit Limit After Cancel Order
                $this->updateCustomerCreditLimit();
                $creditMemoId = $this->order->getIncrementId();
            }

            if ($this->order->getStatus() !== 'canceled') {
                $this->commentEmailSender->send($this->order, true);
            }

            return $this->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::SUCCESS,
                "Record Successfully Synced",
                $creditMemoId
            );
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            return $this->setResponse(
                \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                __($ex->getMessage()),
                null
            );
        }
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function processProcessingStatusWithoutInvoice()
    {
        if (!$this->order->hasShipments()) {
            $result = $this->cancelMagentoOrder();
            if (!$result) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __($this->errorMsg)
                );
            }
            //@hrusikesh Updating Customer Credit Limit After Cancel Order
            $this->updateCustomerCreditLimit();

            return $this->order->getIncrementId();
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __("Shipment quantity must be invoiced.")
            );
        }
    }

    /**
     * For processing order
     *
     * @return mixed
     * @throws \Exception
     */
    public function processProcessingStatusWithInvoice()
    {
        if (!$this->order->canInvoice()) {
            $isPartiallyShipped = $this->checkIspartiallyShipped($this->order);
            if (!empty($isPartiallyShipped) && $isPartiallyShipped !== 0) {
                $isPartial = true;
            } else {
                $isPartial = false;
            }

            $creditmemoResult = $this->refundOrder($isPartial);
            if ($creditmemoResult) {
                if (!$creditmemoResult[self::STATUS]) {
                    $this->logger->create()->createLog(
                        '__METHOD__',
                        $creditmemoResult[self::MESSAGE],
                        LoggerInterface::I95EXC,
                        'error'
                    );
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __($creditmemoResult[self::MESSAGE])
                    );
                } else {
                    $this->updateCustomerCreditLimit();
                    $this->updateCustomOrder();
                }
            }
        } else {
            // partial invoiced order
            $creditmemoResult = $this->refundOrder($isPartial = true);
            $this->processProcessingStatusWithPartialInvoice($creditmemoResult);
        }
        return $this->creditMemoId;
    }

    /**
     * @param  $creditmemoResult
     * @throws \Exception
     */
    public function processProcessingStatusWithPartialInvoice($creditmemoResult)
    {
        if ($creditmemoResult) {
            if (!$creditmemoResult[self::STATUS]) {
                $this->logger->create()->createLog(
                    '__METHOD__',
                    $creditmemoResult[self::MESSAGE],
                    LoggerInterface::I95EXC,
                    'error'
                );
                throw new \Magento\Framework\Exception\LocalizedException(
                    __($creditmemoResult[self::MESSAGE])
                );
            } else {
                $this->updateCustomerCreditLimit();
                $this->updateCustomOrder();
                $this->updateOrder($this->order);
            }
        }
    }

    /**
     * validate data
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate()
    {
        $loadCustomOrderDetails = $this->customSalesOrder
            ->create()
            ->getCollection()
            ->addFieldToSelect('id')
            ->addFieldtoFilter("target_order_id", $this->stringData[self::TARGETID])
            ->setOrder('id', 'DESC');
        $loadCustomOrderDetails->getSelect()->limit(1);

        $this->loadCustomOrder = $this->customSalesOrder->create()->load(
            $loadCustomOrderDetails->getFirstItem()->getId()
        );
        if ($this->loadCustomOrder->getId()) {
            $this->order = $this->orderFactory->create()->loadByIncrementId($this->loadCustomOrder->getSourceOrderId());
        } else {
            $message = "order not found";
            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }
        if (!empty($this->order) && $this->order->getEntityId() > 0) {
            $this->orderStatus = $this->order->getStatus();
        } else {
            $message = "Order not exists in Magento.";
            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }
        if (strtolower($this->orderStatus) == 'canceled') {
            $message = "Order already cancelled.";
            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }
        if (strtolower($this->orderStatus) == 'closed') {
            $message = "Order already closed.";
            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }
        //@Hrusieksh Added Completed Order Cancel Validation
        if (strtolower($this->orderStatus) == 'complete') {
            $message = "Completed order cannot be canceled.";
            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }

        /*
         * @addedBy Subhan. To validate partial shipment and partial invoice
         */
        $this->data->validatePartialData($this);
    }

    /**
     * Cancel order
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function cancelMagentoOrder()
    {
        try {
            /**
             * @author Debashis S. Gopal. Added observer skip
             **/
            $this->dataHelper->unsetGlobalValue(self::I95OSKIP);
            $this->dataHelper->setGlobalValue(self::I95OSKIP, true);
            $this->orderManagement->cancel($this->orderId);
            $this->updateCustomOrder();
            $this->dataHelper->unsetGlobalValue(self::I95OSKIP);
            return true;
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $message = $ex->getMessage();
            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }
    }

    /**
     * creates credit memo for cancel order
     *
     * @param  flag $isPartial
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function refundOrder($isPartial)
    {
        $order = $this->order;
        $status = true;
        $paymentDetails = $order->getPayment();
        $paymentMethod = $paymentDetails->getMethod($this->order);
        $paymentAdditionalInfo = $paymentDetails->getAdditionalInformation();

        $isAuthorize = 0;
        if (isset($paymentAdditionalInfo["payment_type"]) && $paymentDetails["payment_type"] == "authorize") {
            $isAuthorize = 1;
        }

        $dataArray = [
            'do_offline' => 0,
            'adjustment_positive' => 0,
            'base_shipping_amount' => 0,
            'adjustment_negative' => 0,
            'refund_customerbalance_return_enable' => 0,
            'send_email' => 1,
        ];

        if ($isPartial) {
            $QtytoReturn = $this->getQtytoReturn($this->order);
            $itemToCredit = $QtytoReturn[self::ITEMS];
            $qtys = $QtytoReturn['qtys'];
            $dataArray['shipping_amount'] = 0;
            $dataArray [self::ITEMS] = $itemToCredit;
            $dataArray['qtys'] = $qtys;
        }

        if (!empty($this->order->getInvoiceCollection())) {
            // @updatedBy Subhan
            $msg[] = 'success';
            return $this->createCreditMemo($paymentMethod, $isAuthorize, $status, $order, $dataArray, $msg);
        } else {
            $msg[] = 'Sorry,Credit memo cant be created';
            $status = false;
            return [self::STATUS => $status, self::MESSAGE => $msg, self::CREDITMEMOID => $this->creditMemoId];
        }
    }

    /**
     * @param  $paymentMethod
     * @param  $isAuthorize
     * @param  $status
     * @param  $order
     * @param  $dataArray
     * @param  $msg
     * @return array
     */
    public function createCreditMemo($paymentMethod, $isAuthorize, $status, $order, $dataArray, $msg)
    {

        $offlinePaymentMethods = ['checkmo', 'cashondelivery', 'free', 'creditlimits'];

        if (in_array($paymentMethod, $offlinePaymentMethods) || $isAuthorize > 0) {
            $result = $this->cancelMagentoOrder();
            if (!$result) {
                throw new \Magento\Framework\Exception\LocalizedException(__($this->errorMsg));
            }
            $this->creditMemoId = $this->order->getIncrementId();
            return [self::STATUS => $status, self::CREDITMEMOID => $this->creditMemoId];
        } else {
            $invoices = $order->getInvoiceCollection();
            foreach ($invoices as $invoice) {
                $invoiceincrementid = $invoice->getIncrementId();
            }

            $invoiceobj = $invoice->loadByIncrementId($invoiceincrementid);
            $creditmemo = $this->creditmemoFactory->createByOrder($order, $dataArray);
            $creditmemo->setInvoice($invoiceobj);

            try {
                if (!$creditmemo->isValidGrandTotal()) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('The credit memo\'s total must be positive.')
                    );
                }
                $creditMemoData = $this->creditmemoService->refund($creditmemo);
                $this->creditMemoId = $creditMemoData->getIncrementId();
                if (!empty($msg)) {
                    $message = implode(',', $msg);
                } else {
                    $message = "";
                }
                return [self::STATUS => $status, self::MESSAGE => $message, self::CREDITMEMOID => $this->creditMemoId];
            } catch (\Magento\Framework\Exception\LocalizedException $ex) {
                throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
            }
        }
    }

    /**
     * updates custom order info
     */
    public function updateCustomOrder()
    {
        $status = $this->erpOrderStatus->getERPOrderStatus($this->orderId);
        $this->loadCustomOrder->setTargetOrderStatus($status);
        $this->loadCustomOrder->setUpdateBy("ERP");
        $this->loadCustomOrder->save();
    }

    /**
     * updates customer credit limit
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function updateCustomerCreditLimit()
    {
        //@ Hrusikesh Changed targetCustomerId to targetId
        $targetCustomerId = isset($this->stringData[self::CUSTOMER][self::TARGETID]) ?
            $this->stringData[self::CUSTOMER][self::TARGETID] : "";
        $customerInfo = $this->getCustomerInfoByTargetId($targetCustomerId);
        if (!empty($customerInfo) && isset($this->stringData[self::CUSTOMER]['creditLimitType'])) {
            $creditLimitType = $this->stringData[self::CUSTOMER]['creditLimitType'];
            $creditLimitAmount = isset($this->stringData[self::CUSTOMER]['creditLimitAmount']) ?
                $this->stringData[self::CUSTOMER]['creditLimitAmount'] : 0;
            $availableLimit = isset($this->stringData[self::CUSTOMER]['availableLimit']) ?
                $this->stringData[self::CUSTOMER]['availableLimit'] : 0;
            $customerId = $customerInfo[0]->getId();
            $customer = $this->customerRepository->getById($customerId);
            $customer->setWebsiteId($this->storeManager->getStore()->getWebsiteId());
            $customer->setStoreId($this->storeManager->getStore()->getStoreId());
            $customer->setCustomAttribute('credit_limit_type', $creditLimitType);
            $customer->setCustomAttribute('credit_limit_amount', $creditLimitAmount);
            $customer->setCustomAttribute('available_limit', $availableLimit);
            try {
                $this->customerRepository->save($customer);
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $ex) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __($ex->getMessage())
                );
            }
        }
    }

    /**
     * get customer info by target id
     *
     * @param  string $targetId
     * @return obj $customerInfo
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerInfoByTargetId($targetId)
    {
        try {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('target_customer_id', $targetId, 'eq')
                ->create();
            $searchResults = $this->customerRepositoryInterfaceFactory->create()->getList($searchCriteria);
            $customerInfo = $searchResults->getItems();
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->create()->createLog(
                __METHOD__,
                $ex->getMessage(),
                \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                self::CRITICAL
            );
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }

        return $customerInfo;
    }

    /**
     * CHeck if order partially shipped
     *
     * @param  $orderData
     * @return int
     */
    public function checkIspartiallyShipped($orderData)
    {
        $isPartial = 0;
        $order = $this->orderRepository->get($orderData->getId());
        //@ Hrusieksh check wheather the order has Shipment or not
        if ($order->hasShipments()) {
            foreach ($order->getAllVisibleItems() as $item) {
                if ($item->getQtyInvoiced() != $item->getQtyShipped()) {
                    $isPartial ++;
                }
            }
        }
        return $isPartial;
    }

    /**
     * calculate quantity to return
     *
     * @param  obj $orderData
     * @return array
     */
    public function getQtytoReturn($orderData)
    {
        $order = $this->orderRepository->get($orderData->getId());
        if (isset($this->stringData['cancelItemEntity'])) {
            $cancelItems = $this->stringData['cancelItemEntity'];
            $orderItemData = [];
            foreach ($order->getAllVisibleItems() as $item) {
                $orderItemData[strtolower($item->getSku())] = $item->getId();
            }

            $itemToCredit = [];
            $qtys = [];
            foreach ($cancelItems as $cancelItem) {
                $itemToCredit[$orderItemData[strtolower($cancelItem['orderItemId'])]] = [
                    'qty' => $cancelItem['quantityToCancel']
                ];
                $qtys[$orderItemData[strtolower($cancelItem['orderItemId'])]] = $cancelItem['quantityToCancel'];
            }

            return [self::ITEMS => $itemToCredit, 'qtys' => $qtys];
        } else {
            return $this->getCancelQtyFromInvoiceHistory($order);
        }
    }

    /**
     * update order state after create credit memo
     *
     * @param  obj $order
     * @throws \Exception
     */
    public function updateOrder($order)
    {
        /**
         * @author Debashis S. Gopal. Added observer skip
         **/
        $this->dataHelper->unsetGlobalValue(self::I95OSKIP);
        $this->dataHelper->setGlobalValue(self::I95OSKIP, true);
        $orderObj = $this->orderFactory->create()->load($order->getId());
        $orderObj->setState(\Magento\Sales\Model\Order::STATE_CLOSED);
        $orderObj->setStatus(\Magento\Sales\Model\Order::STATE_CLOSED);

        try {
            $orderObj->save();
            $this->commentEmailSender->send($orderObj, true, "");
        } catch (\Zend_Mail_Exception $ex) {
            $message = $ex->getMessage();
            $this->logger->create()->createLog(
                __METHOD__,
                $message,
                LoggerInterface::I95EXC,
                self::CRITICAL
            );
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $message = $ex->getMessage();
            $this->logger->create()->createLog(
                __METHOD__,
                $message,
                LoggerInterface::I95EXC,
                self::CRITICAL
            );
        }
        $this->dataHelper->unsetGlobalValue(self::I95OSKIP);
    }

    /**
     * Get canceled item  quantity from order
     *
     * @param  type $orderObj
     * @return array
     * @author Hrusikesh Manna
     */
    public function getCancelQtyFromOrder($orderObj)
    {
        $orderItems = $orderObj->getAllItems();
        $itemToCredit = [];
        $qtys = [];
        foreach ($orderItems as $item) {
            $qtyToCancel = $item->getQtyOrdered() - $item->getQtyInvoiced();
            if ($qtyToCancel > 0) {
                $itemToCredit[$item->getId()] = [
                    'qty' => $qtyToCancel
                ];
                $qtys[$item->getId()] = $qtyToCancel;
            }
        }
        return [self::ITEMS => $itemToCredit, 'qtys' => $qtys];
    }

    /**
     * @param  $orderData
     * @return array
     */
    public function getShipmentIds($orderData)
    {
        $order = $this->orderRepository->get($orderData->getId());

        $shipmentCollection = $order->getShipmentsCollection();
        $shipmentIds = [];
        foreach ($shipmentCollection as $shipment) {
            $customshipment = $this->generic->getCustomShipmentById($shipment['increment_id']);
            $shipmentIds[] = $customshipment->gettargetShipmentId();
        }
        return $shipmentIds;
    }

    /**
     * @param  $orderData
     * @return array
     */
    public function getInvoiceIds($orderData)
    {
        $order = $this->orderRepository->get($orderData->getId());

        $invoiceCollection = $order->getInvoiceCollection();
        $invoiceIds = [];
        foreach ($invoiceCollection as $invoice) {
            $custominvoice = $this->generic->getCustomInvoiceById($invoice['increment_id']);
            $invoiceIds[] = $custominvoice->gettargetInvoiceId();
        }
        return $invoiceIds;
    }

    /**
     * Get Canceled Item Qty from Custom Invoice History
     *
     * @param   $orderObj
     * @return  array[]
     * @addedBy Subhan
     */
    public function getCancelQtyFromInvoiceHistory($orderObj)
    {
        $orderItems = $orderObj->getAllItems();
        $itemToCredit = [];
        $qtys = [];
        foreach ($orderItems as $item) {
            $qtyInvoiced = $this->getQtyInvoicedFromHistory($this->stringData[self::TARGETID], $item->getSku());
            $qtyToCancel = $item->getQtyOrdered() - (int) $qtyInvoiced;
            if ($qtyToCancel > 0) {
                $itemToCredit[$item->getId()] = [
                    'qty' => $qtyToCancel
                ];
                $qtys[$item->getId()] = $qtyToCancel;
            }
        }
        return [self::ITEMS => $itemToCredit, 'qtys' => $qtys];
    }

    /**
     * Get Invoiced Item Qty from Invoice History
     *
     * @param   $orderTargetId
     * @param   $itemSku
     * @return  mixed
     * @addedBy Subhan
     */
    public function getQtyInvoicedFromHistory($orderTargetId, $itemSku)
    {
        $collection = $this->invoiceHistory->create();
        $collection->getSelect()->join(
            ['itemHistory' => $collection->getTable('i95dev_sales_invoice_item_history')],
            'main_table.id = itemHistory.invoice_entity_id',
            ['sum(itemHistory.item_qty) as item_qty']
        );
        $collection->addFieldToFilter('main_table.target_order_id', $orderTargetId);
        $collection->addFieldToFilter('itemHistory.item_sku', $itemSku);
        return $collection->getData()[0]['item_qty'];
    }
}
