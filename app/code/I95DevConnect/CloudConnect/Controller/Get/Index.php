<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\CloudConnect\Controller\Get;

use Magento\Framework\View\Result\PageFactory;
use \I95DevConnect\ShippingMapping\Api\ShippingMappingManagementInterfaceFactory;

/**
 * Controller for rendering data string in Message Queue
 */
class Index extends \Magento\Framework\App\Action\Action
{
    const OUTPUT = "output";
    const MESSAGE = "message";

    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Api\I95DevErpDataRepositoryInterfaceFactory
     */
    public $i95DevErpData;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \I95DevConnect\CloudConnect\Model\Service $service
     * @param \I95DevConnect\ShippingMapping\Model\ShippingMethodMagento $shippingMethodMagento
     * @param \I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory $requestInterface
     * @param \I95DevConnect\CloudConnect\Model\Request $request
     * @param \I95DevConnect\CloudConnect\Model\Logger $logger
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
     * @param \I95DevConnect\ShippingMapping\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \I95DevConnect\CloudConnect\Model\Service $service,
        \I95DevConnect\ShippingMapping\Model\ShippingMethodMagento $shippingMethodMagento,
        \I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory $requestInterface,
        \I95DevConnect\CloudConnect\Model\Request $request,
        \I95DevConnect\CloudConnect\Model\Logger $logger,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \I95DevConnect\ShippingMapping\Helper\Data $helper
    ) {
        $this->service = $service;
        $this->shippingMethodMagento = $shippingMethodMagento;
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
     * @return \Magento\Framework\Controller\Result\Json
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        if ($this->helper->isEnabled()) {
            $devResponse = $this->requestInterface->create();
            $devResponse->setContext(
                $this->request->prepareContextObject("PushData", null)
            );
            $devResponse->setRequestData(json_encode($this->shippingMethodMagento->availableShippingMethod(), true));
            $res = $this->service->makeServiceCall("PushData", null, $devResponse, null, 'Shipping');
            if (!$res->Result) {
                $this->logger->createLog(
                    __METHOD__,
                    $res->Message,
                    "shipping_mapping",
                    \I95DevConnect\CloudConnect\Model\Logger::INFO
                );

                $result->setData([self::OUTPUT => false, self::MESSAGE => $res->Message]);
                return $result;
            } else {
                $data = $this->shippingMethodMagento->availableShippingMethod();
                $result->setData([self::OUTPUT => $data, self::MESSAGE => ""]);
                return $result;
            }
        }

        $result->setData([self::OUTPUT => false]);
        return $result;
    }
}
