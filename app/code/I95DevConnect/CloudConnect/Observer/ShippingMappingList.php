<?php

/**
 * @author Arushi Bansal
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_ShippingMapping
 */

namespace I95DevConnect\CloudConnect\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Observer to get the shipping mapping when cron run
 */
class ShippingMappingList implements ObserverInterface
{

    const SCHEDULER_TYPE = "pushData";

    /**
     * ShippingMappingList constructor.
     * @param \I95DevConnect\ShippingMapping\Api\ShippingMappingManagementInterfaceFactory $shippingMappingMgmt
     * @param \I95DevConnect\ShippingMapping\Helper\Data $helper
     * @param \I95DevConnect\CloudConnect\Model\RequestFactory $request
     * @param \I95DevConnect\CloudConnect\Model\Logger $logger
     */
    public function __construct(
        \I95DevConnect\ShippingMapping\Api\ShippingMappingManagementInterfaceFactory $shippingMappingMgmt,
        \I95DevConnect\ShippingMapping\Helper\Data $helper,
        \I95DevConnect\CloudConnect\Model\RequestFactory $request,
        \I95DevConnect\CloudConnect\Model\Logger $logger
    ) {
        $this->shippingMappingMgmt = $shippingMappingMgmt;
        $this->helper = $helper;
        $this->request = $request;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        if ($this->helper->isEnabled()) {
            $currentObj = $observer->getEvent()->getData("currentObject");
            $schedulerId = $observer->getEvent()->getData("schedulerId");

            if ($currentObj->schedulerData->IsShippingMappingUpdated) {

                $shippingMappingData = $currentObj->service->makeServiceCall(
                    self::SCHEDULER_TYPE,
                    null,
                    null,
                    $schedulerId,
                    'ShippingList'
                );

                if ($shippingMappingData != '' && isset($shippingMappingData->ResultData)) {

                    $this->shippingMappingMgmt->create()->processMappingData($shippingMappingData->ResultData);

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
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendAck($schedulerType, $schedulerId, $currentObj)
    {

        $devReq = $currentObj->requestInterface->create();

        $devReq->setContext(
            $this->request->create()->prepareContextObject('pullData', $schedulerId)
        );

        $devReq->setType('shippingMappingUpdate');

        //sending entityAck to cloud
        $result = $currentObj->service
            ->makeServiceCall($schedulerType, null, $devReq, $schedulerId, 'Ack');

        if (!$result->IsShippingMappingUpdated) {
            $this->logger->createLog(
                "PullData entity ACK",
                "Shipping Mapping updated in cloud",
                "shipping_mapping",
                \I95DevConnect\CloudConnect\Model\Logger::INFO
            );
        }
        return true;
    }
}
