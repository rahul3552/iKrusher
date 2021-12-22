<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\CloudConnect\Controller\Shipping;

use Magento\Framework\View\Result\PageFactory;
use \I95DevConnect\ShippingMapping\Api\ShippingMappingManagementInterfaceFactory;
use \I95DevConnect\ShippingMapping\Api\Data\ShippingMappingDataInterfaceFactory;

/**
 * Controller for rendering data string in Message Queue
 */
class SendMapping extends \Magento\Framework\App\Action\Action
{

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
     * @param ShippingMappingDataInterfaceFactory $shippingMappingData
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \I95DevConnect\CloudConnect\Model\Service $service,
        \I95DevConnect\ShippingMapping\Model\ShippingMethodMagento $shippingMethodMagento,
        \I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory $requestInterface,
        \I95DevConnect\CloudConnect\Model\Request $request,
        \I95DevConnect\CloudConnect\Model\Logger $logger,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \I95DevConnect\ShippingMapping\Helper\Data $helper,
        ShippingMappingDataInterfaceFactory $shippingMappingData
    ) {
        $this->service = $service;
        $this->shippingMethodMagento = $shippingMethodMagento;
        $this->requestInterface = $requestInterface;
        $this->request = $request;
        $this->logger = $logger;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->helper = $helper;
        $this->shippingMappingData = $shippingMappingData;

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
        $outputLiteral = "output";
        if ($this->helper->isEnabled()) {
            $mappingData = $this->shippingMappingData->create()->getCollection()->getData();

            $devResponse = $this->requestInterface->create();
            $devResponse->setContext(
                $this->request->prepareContextObject("PushData", null)
            );

            $formattedMappingData = [];
            foreach ($mappingData as $value) {
                $convertData = [];
                $convertData['ecommerceMethod'] = $value['magento_code'];
                $convertData['erpMethod'] = $value['erp_code'];
                $convertData['isEcommerceDefault'] = empty($value['is_ecommerce_default']);
                $convertData['isErpDefault'] = empty($value['is_erp_default']);

                $formattedMappingData[] = $convertData;
            }

            $devResponse->setRequestData(json_encode($formattedMappingData, true));
            $res = $this->service->makeServiceCall("PushData", null, $devResponse, null, 'ShippingDefault');

            if (!$res->Result) {
                $this->logger->createLog(
                    __METHOD__,
                    $res->Message,
                    "shipping_mapping",
                    \I95DevConnect\CloudConnect\Model\Logger::INFO
                );

                $result->setData([$outputLiteral => false]);
                return $result;
            } else {
                $result->setData([$outputLiteral => json_encode($formattedMappingData, true)]);
                return $result;
            }
        }

        $result->setData([$outputLiteral => false]);
        return $result;
    }
}
