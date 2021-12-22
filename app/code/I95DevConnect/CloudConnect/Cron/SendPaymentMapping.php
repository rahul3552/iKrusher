<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95DevConnect\CloudConnect\Cron;

use I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory;
use I95DevConnect\CloudConnect\Model\Logger;
use I95DevConnect\CloudConnect\Model\Request;
use I95DevConnect\CloudConnect\Model\Service;
use I95DevConnect\PaymentMapping\Helper\Data;
use I95DevConnect\PaymentMapping\Model\PaymentMethodMagento;

/**
 * class for send payment mapping to cloud
 */
class SendPaymentMapping
{

    const SCHEDULER_TYPE = 'PushData';
    private $logFilename = 'PaymentMapping';
    /**
     * @var Service
     */
    public $service;
    /**
     * @var RequestInterfaceFactory
     */
    public $requestInterface;
    /**
     * @var Request
     */
    public $request;
    /**
     * @var Logger
     */
    public $logger;
    /**
     * @var PaymentMethodMagento
     */
    public $paymentMethodMagento;
    /**
     * @var Data
     */
    public $helper;

    /**
     * SendPaymentMapping constructor.
     * @param Service $service
     * @param RequestInterfaceFactory $requestInterface
     * @param Request $request
     * @param Logger $logger
     * @param PaymentMethodMagento $paymentMethodMagento
     * @param Data $helper
     */
    public function __construct(
        Service $service,
        RequestInterfaceFactory $requestInterface,
        Request $request,
        Logger $logger,
        PaymentMethodMagento $paymentMethodMagento,
        Data $helper
    ) {
        $this->service = $service;
        $this->requestInterface = $requestInterface;
        $this->request = $request;
        $this->logger = $logger;
        $this->paymentMethodMagento = $paymentMethodMagento;
        $this->helper = $helper;
    }

    public function execute()
    {
        if ($this->helper->isEnabled()) {
            $devResponse = $this->requestInterface->create();
            $devResponse->setContext(
                $this->request->prepareContextObject(self::SCHEDULER_TYPE, null)
            );
            $devResponse->setRequestData(
                json_encode($this->paymentMethodMagento->availablePaymentMethods(), true)
            );

            $res = $this->service->makeServiceCall(
                self::SCHEDULER_TYPE,
                null,
                $devResponse,
                null,
                'Payment'
            );

            if (!$res->Result) {
                $this->logger->createLog(
                    __METHOD__,
                    $res->Message,
                    $this->logFilename,
                    Logger::INFO
                );
            }
        }
    }
}
