<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\Http;

/**
 * Observer class to sales order before save
 */
class SalesOrderSaveAfterObserver implements ObserverInterface
{

    const MAGLOGNAME = 'MagentoToERP';
    const ERPLOGNAME = 'ERPToMagento';
    const I95EXC = 'i95devApiException';
    const PAYMENTMETHOD = 'checkmo';
    const ORDER_OBSERVER_SKIP = 'order_observer_skip';
    const CRITICAL = 'critical';

    /**
     * @var Registry
     */
    public $coreRegistry;

    /**
     * @var \Magento\Sales\Model\Order
     */
    public $salesOrderModel;

    /**
     * @var customSalesOrder
     */
    public $customSalesOrder;

    /**
     * @var I95DevConnect\MessageQueue\Model\ChequeNumberFactory
     */
    public $chequeNumberFactory;

    public $eventManager;

    public $dataHelper;
    public $request;

    /**
     *
     * @param \Magento\Sales\Model\Order $salesOrderModel
     * @param \I95DevConnect\MessageQueue\Model\SalesOrderFactory $customSalesOrder
     * @param \I95DevConnect\MessageQueue\Model\ChequeNumberFactory $chequeNumberFactory
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Sales\Model\Order $salesOrderModel,
        \I95DevConnect\MessageQueue\Model\SalesOrderFactory $customSalesOrder,
        \I95DevConnect\MessageQueue\Model\ChequeNumberFactory $chequeNumberFactory,
        \Magento\Framework\Event\Manager $eventManager,
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \Magento\Framework\Registry $coreRegistry,
        Http $request
    ) {
        $this->dataHelper = $dataHelper;
        $this->coreRegistry = $coreRegistry;
        $this->salesOrderModel = $salesOrderModel;
        $this->customSalesOrder = $customSalesOrder;
        $this->chequeNumberFactory = $chequeNumberFactory;
        $this->eventManager=$eventManager;
        $this->request = $request;
    }

    /**
     * Save i95Dev Custom attributes
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @throws \Exception
     * @author i95Dev Team
     * @updatedBy Divya Koona. Removed of inserting gp_orderprocess_flag column value to i95dev_sales_flat_order table
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            if ($this->skipObserver()) {
                return;
            }

            $orderObserverData = $observer->getEvent()->getDataObject();
            $orderId = $orderObserverData->getIncrementId();
            if (!empty($orderId) && $orderId != "") {
                $order = $this->salesOrderModel->loadByIncrementId($orderId);
                if ($order->getIsGuest() && $order->getCustomerId() !== null) {
                    $this->dataHelper->logger->createLog(__METHOD__, "Duplicate order to Outbound MQ on guest customer"
                        . " conversion to registered from Order sucess page.", self::I95EXC, self::CRITICAL);
                } else {
                    $realOrderId = $order->getIncrementId();
                    $orderData = $order->getData();
                    $orderStatus = $orderData['status'];
                    if ($orderStatus == "canceled" && $orderStatus == "closed") {
                        $this->dataHelper->createLog(
                            __METHOD__,
                            "Order is Canceled in Magento",
                            self::I95EXC,
                            self::CRITICAL
                        );
                        return ;
                    }
                    $paymentMethod = "";
                    $paymentMethodData = $orderObserverData->getPayment();
                    $paymentMethod = $this->getPaymentMethod($paymentMethodData);

                    if ($paymentMethod == self::PAYMENTMETHOD) {
                        $requestParameters = $paymentMethodData['additional_information'];

                        $magChequeNumber = $this->getMagChequeNumber($requestParameters, $paymentMethodData);
                        $orderId = $this->getOrderId($paymentMethodData, $order);
                        $chequeNumber = $this->getChequeNumber($magChequeNumber, $paymentMethodData);
                        $this->saveChequeNumber($chequeNumber, $orderId);
                    }
                    $this->setCustomOrder($realOrderId, $orderData);
                    $this->eventManager->dispatch('creditlimit_event_before', ['myEventData'=>$order]);
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->dataHelper->logger->createLog(__METHOD__, $ex->getMessage(), self::I95EXC, self::CRITICAL);
        }
    }

    /**
     * @param $realOrderId
     * @param $orderData
     */
    public function setCustomOrder($realOrderId, $orderData)
    {
        $customOrder = $this->customSalesOrder->create();
        $loadCustomOrder=$this->customSalesOrder->create()->load($realOrderId, 'source_order_id');
        if ($loadCustomOrder->getId()) {
            $customOrder->setId($loadCustomOrder->getId());
        }
        $customOrder->setSourceOrderId($realOrderId);
        $customOrder->setTargetOrderStatus('New');
        $customOrder->setOrigin('website');
        $customOrder->setCreatedAt($orderData['created_at']);
        $customOrder->setUpdatedAt($orderData['updated_at']);
        $customOrder->setUpdateBy('Magento');
        $customOrder->save();
    }

    /**
     *
     */
    public function skipObserver()
    {
        $is_enabled = $this->dataHelper->isEnabled();
        if (!$is_enabled) {
            return true;
        }

        if ($this->dataHelper->getGlobalValue('i95_observer_skip') ||
            $this->request->getParam('isI95DevRestReq') == 'true' ||
            ($this->dataHelper->getGlobalValue(self::ORDER_OBSERVER_SKIP) == 'invoice' ||
                $this->dataHelper->getGlobalValue(self::ORDER_OBSERVER_SKIP) == 'shipment')) {
            $this->dataHelper->unsetGlobalValue(self::ORDER_OBSERVER_SKIP);
            return true;
        }

        return false;
    }

    /**
     * @param $magChequeNumber
     * @param $paymentMethodData
     * @return mixed
     */
    public function getChequeNumber($magChequeNumber, $paymentMethodData)
    {
        return (isset($magChequeNumber) ?
            $magChequeNumber : $paymentMethodData['target_cheque_number']);
    }

    /**
     * @param $requestParameters
     * @param $paymentMethodData
     * @return mixed
     */
    public function getMagChequeNumber($requestParameters, $paymentMethodData)
    {
        return (isset($requestParameters['Checknumber']) ?
            $requestParameters['Checknumber'] : $paymentMethodData->getCheckNumber());
    }

    /**
     * @param $paymentMethodData
     * @param $order
     * @return mixed
     */
    public function getOrderId($paymentMethodData, $order)
    {
        $orderId = (isset($paymentMethodData['parent_id']) ?
            $paymentMethodData['parent_id'] : $order->getId());
        if ($orderId == '') {
            $orderId = $order->getId();
        }

        return $orderId;
    }

    /**
     * saves cheque number
     * @param type $chequeNumber
     * @param type $orderId
     * @author i95Dev Team
     */
    private function saveChequeNumber($chequeNumber, $orderId)
    {
        if ($chequeNumber) {
            $checkNumber = $this->chequeNumberFactory->create();
            $checkNumber->setTargetChequeNumber($chequeNumber)
                ->setSourceOrderId($orderId)
                ->save();
        }
    }

    /**
     * @param $paymentMethodData
     * @return string
     */
    public function getPaymentMethod($paymentMethodData)
    {
        $paymentMethod = "";
        if ($paymentMethodData) {
            $paymentMethod = $paymentMethodData->getMethod();
        }

        return $paymentMethod;
    }
}
