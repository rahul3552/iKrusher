<?php

namespace I95DevConnect\CloudConnect\Cron;

/**
 * Class for Sending Shipping mapping data
 */
class SendShippingmapping
{

    const SCHEDULER_TYPE = 'PushData';
    private $logFilename = 'shippingMapping';

    /**
     * SendShippingmapping constructor.
     * @param \I95DevConnect\CloudConnect\Model\Service $service
     * @param \Magento\Shipping\Model\Config $shipconfig
     * @param \I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory $requestInterface
     * @param \I95DevConnect\CloudConnect\Model\Request $request
     * @param \I95DevConnect\CloudConnect\Model\Logger $logger
     * @param \I95DevConnect\ShippingMapping\Model\ShippingMethodMagento $shippingMethodMagento
     * @param \I95DevConnect\ShippingMapping\Helper\Data $helper
     */
    public function __construct(
        \I95DevConnect\CloudConnect\Model\Service $service,
        \Magento\Shipping\Model\Config $shipconfig,
        \I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory $requestInterface,
        \I95DevConnect\CloudConnect\Model\Request $request,
        \I95DevConnect\CloudConnect\Model\Logger $logger,
        \I95DevConnect\ShippingMapping\Model\ShippingMethodMagento $shippingMethodMagento,
        \I95DevConnect\ShippingMapping\Helper\Data $helper
    ) {
        $this->service = $service;
        $this->shipconfig = $shipconfig;
        $this->requestInterface = $requestInterface;
        $this->request = $request;
        $this->logger = $logger;
        $this->shippingMethodMagento = $shippingMethodMagento;
        $this->helper = $helper;
    }

    public function execute()
    {
        if ($this->helper->isEnabled()) {
            $devResponse = $this->requestInterface->create();
            $devResponse->setContext(
                $this->request->prepareContextObject(self::SCHEDULER_TYPE, null)
            );
            $devResponse->setRequestData(json_encode($this->shippingMethodMagento->availableShippingMethod(), true));

            $res = $this->service->makeServiceCall(self::SCHEDULER_TYPE, null, $devResponse, null, 'Shipping');

            if (!$res->Result) {
                $this->logger->createLog(
                    __METHOD__,
                    $res->Message,
                    $this->logFilename,
                    \I95DevConnect\CloudConnect\Model\Logger::INFO
                );
            }
        }
    }
}
