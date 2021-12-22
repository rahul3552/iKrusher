<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95DevConnect\CloudConnect\Model\ServiceMethod;

use I95DevConnect\CloudConnect\Model\Logger;

/**
 * Class to connect cloud server
 */
class ServiceMethod
{

    public $logger;
    private $configHelper;
    private $service;
    public $cloudHelper;
    public $connectServiceRoute;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    public $jsonHelper;

    /**
     * Constructor for DI
     * @param Logger $logger
     * @param \I95DevConnect\CloudConnect\Helper\ConfigHelper $configHelper
     * @param \I95DevConnect\CloudConnect\Model\Service $service
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \I95DevConnect\CloudConnect\Helper\Data $cloudHelper
     * @param mixed $connectServiceRoute
     */
    public function __construct(
        Logger $logger,
        \I95DevConnect\CloudConnect\Helper\ConfigHelper $configHelper,
        \I95DevConnect\CloudConnect\Model\Service $service,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \I95DevConnect\CloudConnect\Helper\Data $cloudHelper,
        $connectServiceRoute
    ) {
        $this->logger = $logger;
        $this->cloudHelper = $cloudHelper;
        $this->configHelper = $configHelper;
        $this->service = $service;
        $this->jsonHelper = $jsonHelper;
        $this->connectServiceRoute = $connectServiceRoute;
    }

    /**
     * Method to connect Cloud
     * @param string $request
     * @param string $entity
     * @param string $requestType
     * @param string $syncType
     * @param string $action
     * @return object
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function cloudConnect($request, $entity, $requestType, $syncType, $action = null)
    {
        try {
            $erpCrmType = $this->configHelper->getSelctErpCrmConfig();
            if ($erpCrmType == \I95DevConnect\CloudConnect\Model\Config\Source\CrmErp::CLOUD
                && isset($this->connectServiceRoute[$syncType])) {
                $this->connectServiceRoute = $this->connectServiceRoute[$syncType];

                return $this->connectServiceRoute['classObject']->sync(
                    $request,
                    $entity,
                    $requestType,
                    $action
                );
            }
        } catch (\Exception $e) {
            $this->logger->createLog(
                __METHOD__,
                $e->getMessage(),
                Logger::EXCEPTION,
                Logger::CRITICAL
            );
        }
    }

    /**
     * Method to get API Method objects to receive data from Cloud
     * @param array $apiMethodsRoutes
     * @param string $syncType
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getReverseApiMethods($apiMethodsRoutes, $syncType)
    {
        $apiMethods = [];
        try {
            foreach ($apiMethodsRoutes as $serviceMethod => $data) {
                if ($data['methodType'] == $syncType) {
                    $etityCode = $data['entityCode'];
                    if (isset($data['classObject'])) {
                        $apiMethods[$etityCode] = $serviceMethod;
                    } else {
                        if (!array_key_exists($etityCode, $apiMethods)) {
                            $apiMethods[$etityCode] = $serviceMethod;
                        }
                    }
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->createLog(
                __METHOD__,
                $e->getMessage(),
                Logger::EXCEPTION,
                Logger::CRITICAL
            );
        }

        return $apiMethods;
    }

    /**
     * Method to get API method objects to send information to cloud
     * @param array $apiMethodsRoutes
     * @param string $syncType
     * @param string $action
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getForwardApiMethods($apiMethodsRoutes, $syncType, $action)
    {
        $apiMethods = [];
        try {
            foreach ($apiMethodsRoutes as $serviceMethod => $data) {
                if ($data['methodType'] == $syncType && isset($data[$action])) {
                    $apiMethods[$data['entityCode']] = $serviceMethod;
                }
            }
        } catch (\Exception $e) {
            $this->logger->createLog(
                __METHOD__,
                $e->getMessage(),
                Logger::EXCEPTION,
                Logger::CRITICAL
            );
        }

        return $apiMethods;
    }
}
