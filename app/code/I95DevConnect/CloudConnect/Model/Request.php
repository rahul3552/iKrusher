<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95DevConnect\CloudConnect\Model;

use Magento\Framework\Model\AbstractModel;
use I95DevConnect\CloudConnect\Model\Logger;

/**
 * class to get request from cloud to magento
 */
class Request extends AbstractModel
{

    public $logger;
    public $requestInterface;
    public $contextInterface;
    public $dataInterface;

    /**
     * scopeConfig for system Configuration
     *
     * @var string
     */
    public $scopeConfig;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    public $jsonHelper;

    /**
     * Constructor for DI
     * @param Logger $logger
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory $requestInterface
     * @param \I95DevConnect\CloudConnect\Api\Data\ContextInterfaceFactory $contextInterface
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \I95DevConnect\CloudConnect\Helper\Data $cloudHelper
     */
    public function __construct(
        Logger $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory $requestInterface,
        \I95DevConnect\CloudConnect\Api\Data\ContextInterfaceFactory $contextInterface,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \I95DevConnect\CloudConnect\Helper\Data $cloudHelper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->requestInterface = $requestInterface;
        $this->contextInterface = $contextInterface;
        $this->jsonHelper = $jsonHelper;
        $this->cloudHelper = $cloudHelper;
    }

    /**
     * Method to create object for cloud request
     * @param string $schedulerType
     * @param string $entity
     * @param string $schedulerId
     * @return object
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function cloudRequest($schedulerType, $entity, $schedulerId)
    {
        $request = $this->requestInterface->create();
        try {
            if ($entity == null) {
                $request->setContext($this->prepareContextObject($schedulerType, $schedulerId));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->createLog(
                __METHOD__,
                $e->getMessage(),
                Logger::EXCEPTION,
                'critical'
            );
        }

        return $request;
    }

    /**
     * Method to prepare Context object
     * @param string $schedulerType
     * @param string $schedulerId
     * @return object
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareContextObject($schedulerType, $schedulerId)
    {
        $context = $this->contextInterface->create();
        try {
            $clientId = $this->cloudHelper->getConfigClientId();
            $subscriptionKey = $this->cloudHelper->getConfigSubscriptionKey();
            $endpointCode = $this->cloudHelper->getConfigEndpointCode();
            $instanceType = $this->cloudHelper->getInstanceType();
            if (!empty($schedulerId)) {
                $context->setSchedulerId($schedulerId);
            }
            $context->setRequestType("Source");
            $context->setSchedulerType($schedulerType);
            $context->setClientId($clientId);
            $context->setSubscriptionKey($subscriptionKey);
            $context->setEndpointCode($endpointCode);
            $context->setInstanceType($instanceType);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->createLog(
                __METHOD__,
                $e->getMessage(),
                Logger::EXCEPTION,
                'critical'
            );
        }
        return $context;
    }
}
