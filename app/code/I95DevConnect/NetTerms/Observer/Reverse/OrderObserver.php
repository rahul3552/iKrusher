<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Observer\Reverse;

use Magento\Framework\Exception\LocalizedException;
use I95DevConnect\MessageQueue\Model\SalesOrderFactory;
use I95DevConnect\NetTerms\Helper\Data;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;

/**
 * Observer to save data after order save
 */
class OrderObserver implements ObserverInterface
{

    /**
     * @var Order
     */
    public $salesOrderModel;
    public $dataHelper;

    /**
     * @var $customSalesOrder
     */
    public $customSalesOrder;

    /**
     * @var DateTime
     */
    public $date;
    public $netTermsHelper;
    public $customerFactory;

    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        DateTime $date,
        SalesOrderFactory $customSalesOrder,
        OrderFactory $salesOrderModel,
        Data $netTermsHelper,
        CustomerFactory $customerFactory
    ) {

        $this->dataHelper = $dataHelper;
        $this->salesOrderModel = $salesOrderModel;
        $this->date = $date;
        $this->customSalesOrder = $customSalesOrder;
        $this->netTermsHelper = $netTermsHelper;
        $this->customerFactory = $customerFactory;
    }

    /**
     * Save i95Dev Custom attributes
     *
     * @param Observer $observer
     *
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $isEnabled = $this->netTermsHelper->isNetTermsEnabled();
        if (!$isEnabled) {
            return;
        }

        $orderObserverData = $observer->getEvent()->getData('orderObject');
        $targetNetTermsId = $orderObserverData->dataHelper
            ->getValueFromArray("targetNetTermsId", $orderObserverData->stringData);
        $orderId = $orderObserverData->dataHelper
            ->getValueFromArray("targetOrderId", $orderObserverData->stringData);


        $is_enabled = $this->netTermsHelper->isNetTermsLabelEnabled();
        if ($is_enabled) {
            $payment = $orderObserverData->dataHelper
                ->getValueFromArray("payment", $orderObserverData->stringData);
            $paymentMethod = $payment[0]['paymentMethod'];
            if (!empty($targetNetTermsId) && $paymentMethod == "credlmt") {
                $salesOrder = $this->salesOrderModel->create()->load($orderObserverData->orderObject['entity_id']);
                $salesOrder->getPayment()->setAdditionalInformation("method_title", $targetNetTermsId);
                $salesOrder->save();
            }
        }
        $loadCustomOrder = $this->customSalesOrder->create()->load($orderId, 'target_order_id');
        // print_r($loadCustomOrder->getData());
        // echo $targetNetTermsId;
        // exit;
        $loadCustomOrder->setData('net_terms_id', $targetNetTermsId)
            ->setData('update_by', 'ERP')
            ->setData('updated_at', $this->date->gmtDate())
            ->save();
            //exit;
    }
}
