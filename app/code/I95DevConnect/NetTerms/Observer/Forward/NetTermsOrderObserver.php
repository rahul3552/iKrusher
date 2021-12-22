<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Observer\Forward;

use Magento\Framework\Exception\LocalizedException;
use I95DevConnect\MessageQueue\Api\LoggerInterface;
use I95DevConnect\MessageQueue\Model\SalesOrderFactory;
use I95DevConnect\NetTerms\Helper\Data;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\OrderFactory;

/**
 * Save net terms id to a customer on order save
 */
class NetTermsOrderObserver implements ObserverInterface
{
    public $netTermsHelper;
    public $mqHelper;
    public $request;
    public $customerFactory;
    public $logger;
    public $customSalesOrder;

    const NETTERMID = "net_terms_id";
    const I95EXC = "i95devApiException";

    /**
     * @param Data $netTermsHelper
     * @param \I95DevConnect\MessageQueue\Helper\Data $mqHelper
     * @param RequestInterface $request
     * @param SalesOrderFactory $customSalesOrder
     * @param CustomerFactory $customerFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Data $netTermsHelper,
        \I95DevConnect\MessageQueue\Helper\Data $mqHelper,
        RequestInterface $request,
        SalesOrderFactory $customSalesOrder,
        CustomerFactory $customerFactory,
        LoggerInterface $logger
    ) {
        $this->netTermsHelper = $netTermsHelper;
        $this->mqHelper = $mqHelper;
        $this->request = $request;
        $this->customerFactory = $customerFactory;
        $this->customSalesOrder = $customSalesOrder;
        $this->logger = $logger;
    }

    /**
     *  i95Dev Custom attributes
     *
     * @param  Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $is_enabled = $this->netTermsHelper->isNetTermsEnabled();
        if (!$is_enabled) {
            return;
        }

        if ($this->mqHelper->getGlobalValue('i95_observer_skip')) {
            return;
        }

        try {
            $order = $observer->getEvent()->getData('myEventData');
            $data = $this->request->getParams();
            $orderData = $order->getData();
            $orderId = $order->getIncrementId();
            $customerId = isset($orderData['customer_id']) ? $orderData['customer_id'] : "";
            $customerData = $this->customerFactory->create()->load($customerId);
            $netTermsId = isset($customerData[self::NETTERMID]) ? $customerData[self::NETTERMID] : "";
            if (isset($data['order']['account'][self::NETTERMID])) {
                $netTermsId = $data['order']['account'][self::NETTERMID];
            }
            if ($netTermsId != "") {
                $is_enabled = $this->netTermsHelper->isNetTermsLabelEnabled();
                if ($is_enabled) {
                    $payment = $order->getPayment();
                    if ($payment) {
                        $payment->setAdditionalInformation(['method_title' => $netTermsId]);
                        $payment->save();
                    }
                }
                $loadCustomOrder = $this->customSalesOrder->create()->load($orderId, 'source_order_id');
                if ($loadCustomOrder->getId()) {
                    $loadCustomOrder->setNetTermsId($netTermsId);
                    $loadCustomOrder->save();
                }
            }
        } catch (LocalizedException $ex) {
            $this->logger->createLog(__METHOD__, $ex->getMessage(), self::I95EXC, 'critical');
        }
    }
}
