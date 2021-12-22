<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95DevConnect\CloudConnect\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class for saving Payment Mapping List
 */
class PaymentMappingList implements ObserverInterface
{

    const SCHEDULER_TYPE = "pushData";
    public $paymentMappingMgmt;
    public $helper;

    /**
     * @param \I95DevConnect\PaymentMapping\Api\PaymentMappingManagementInterfaceFactory $paymentMappingMgmt
     * @param \I95DevConnect\PaymentMapping\Helper\Data $helper
     */
    public function __construct(
        \I95DevConnect\PaymentMapping\Api\PaymentMappingManagementInterfaceFactory $paymentMappingMgmt,
        \I95DevConnect\PaymentMapping\Helper\Data $helper
    ) {
        $this->paymentMappingMgmt = $paymentMappingMgmt;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isEnabled() && $this->helper->isCloudEnabled()) {
            $currentObj = $observer->getEvent()->getData("currentObject");
            $schedulerId = $observer->getEvent()->getSchedulerId();

            if ($currentObj->schedulerData->IsPaymentMappingUpdated) {
                $paymentMappingData = $currentObj->service->makeServiceCall(
                    self::SCHEDULER_TYPE,
                    null,
                    null,
                    $schedulerId,
                    'PaymentList'
                );
                if ($paymentMappingData != '' && isset($paymentMappingData->ResultData)) {
                    $this->paymentMappingMgmt->create()->processMappingData($paymentMappingData->ResultData);
                    $this->sendAck(
                        self::SCHEDULER_TYPE,
                        $schedulerId,
                        $currentObj
                    );
                }
            }
        }
    }

    /**
     * Method to send ACK for Entity status update
     * @param $schedulerType
     * @param $schedulerId
     * @param $currentObj
     * @return boolean
     */
    public function sendAck($schedulerType, $schedulerId, $currentObj)
    {
        $devReq = $currentObj->requestInterface->create();
        $devReq->setContext(
            $currentObj->request->create()->prepareContextObject('pullData', $schedulerId)
        );
        $devReq->setType('paymentMappingUpdate');
        //sending entityAck to cloud
        $result = $currentObj->service
            ->makeServiceCall($schedulerType, null, $devReq, $schedulerId, 'Ack');

        if (!$result->IsPaymentMappingUpdated) {
            $currentObj->logger->create()->createLog(
                "PullData entity ACK",
                "Payment Mapping updated in cloud",
                "payment_mapping",
                \I95DevConnect\CloudConnect\Model\Logger::INFO
            );
        }
        return true;
    }
}
