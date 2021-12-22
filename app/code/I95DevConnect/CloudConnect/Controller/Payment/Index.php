<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PaymentMapping
 */

namespace I95DevConnect\CloudConnect\Controller\Payment;

use Exception;
use I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory;
use I95DevConnect\CloudConnect\Model\Request;
use I95DevConnect\CloudConnect\Model\Service;
use I95DevConnect\MessageQueue\Api\I95DevErpDataRepositoryInterfaceFactory;
use I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory;
use I95DevConnect\PaymentMapping\Helper\Data;
use I95DevConnect\PaymentMapping\Model\PaymentMethodMagento;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;

/**
 * Controller for rendering data string in Message Queue
 */
class Index extends Action
{
    const OUTPUT = "output";
    const MESSAGE = "message";
    
    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     *
     * @var I95DevErpDataRepositoryInterfaceFactory
     */
    public $i95DevErpData;
    /**
     * @var Service
     */
    public $service;
    /**
     * @var PaymentMethodMagento
     */
    public $paymentMethodMagento;
    /**
     * @var RequestInterfaceFactory
     */
    public $requestInterface;
    /**
     * @var Request
     */
    public $request;
    /**
     * @var LoggerInterfaceFactory
     */
    public $logger;
    /**
     * @var JsonFactory
     */
    public $jsonResultFactory;
    /**
     * @var Data
     */
    public $helper;

    /**
     * @param Context $context
     * @param Service $service
     * @param PaymentMethodMagento $paymentMethodMagento
     * @param RequestInterfaceFactory $requestInterface
     * @param Request $request
     * @param LoggerInterfaceFactory $logger
     * @param JsonFactory $jsonResultFactory
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        Service $service,
        PaymentMethodMagento $paymentMethodMagento,
        RequestInterfaceFactory $requestInterface,
        Request $request,
        LoggerInterfaceFactory $logger,
        JsonFactory $jsonResultFactory,
        Data $helper
    ) {
        $this->service = $service;
        $this->paymentMethodMagento = $paymentMethodMagento;
        $this->requestInterface = $requestInterface;
        $this->request = $request;
        $this->logger = $logger;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->helper = $helper;

        parent::__construct($context);
    }

    /**
     * Render Message Queue data string
     *
     * @return int|Json
     * @throws LocalizedException
     */
    public function execute()
    {
        try {
            if ($this->helper->isEnabled()) {
                $devResponse = $this->requestInterface->create();
                $devResponse->setContext(
                    $this->request->prepareContextObject("PushData", null)
                );
                $devResponse->setRequestData(
                    json_encode($this->paymentMethodMagento->availablePaymentMethods(), true)
                );
                $res = $this->service->makeServiceCall("PushData", null, $devResponse, null, 'Payment');
                $result = $this->jsonResultFactory->create();
                if (!$res->Result) {
                    $this->logger->create()->createLog(
                        __METHOD__,
                        $res->Message,
                        "PaymentMapping",
                        'info'
                    );

                    $result->setData([self::OUTPUT => false, self::MESSAGE => $res->Message]);
                    return $result;
                } else {
                    $data = $this->paymentMethodMagento->availablePaymentMethods();
                    $result->setData([self::OUTPUT => $data, self::MESSAGE => ""]);
                    return $result;
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            throw new LocalizedException(__($e->getMessage()));
        }

        return 0;
    }
}
