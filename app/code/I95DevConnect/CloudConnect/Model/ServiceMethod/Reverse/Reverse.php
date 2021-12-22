<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95DevConnect\CloudConnect\Model\ServiceMethod\Reverse;

use I95DevConnect\CloudConnect\Model\Logger;

/**
 * Model class to get data from cloud
 */
class Reverse extends \I95DevConnect\CloudConnect\Model\ServiceMethod\ServiceMethod
{

    private $i95DevRepository;
    public $cloudHelper;
    public $i95DevResponse;
    public $requestInterface;
    public $mqData;
    public $messageQueueModel;
    public $magentoMessageQueue;

    /**
     * scopeConfig for system Configuration
     *
     * @var string
     */
    public $scopeConfig;
    public $translate;
    public $erpName = 'ERP';
    public $logger;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    public $jsonHelper;
    public $dataInterface;
    public $request;
    public $data;

    /**
     * Constructor for DI
     * @param \I95DevConnect\CloudConnect\Helper\Data $cloudHelper
     * @param \I95DevConnect\I95DevServer\Api\I95DevServerRepositoryInterfaceFactory $i95DevRepository
     * @param \I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory $requestInterface
     * @param \I95DevConnect\CloudConnect\Model\RequestFactory $request
     * @param Logger $logger
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \I95DevConnect\CloudConnect\Helper\Data $cloudHelper,
        \I95DevConnect\I95DevServer\Api\I95DevServerRepositoryInterfaceFactory $i95DevRepository,
        \I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory $requestInterface,
        \I95DevConnect\CloudConnect\Model\RequestFactory $request,
        Logger $logger,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->cloudHelper = $cloudHelper;
        $this->i95DevRepository = $i95DevRepository;
        $this->requestInterface = $requestInterface;
        $this->request = $request;
        $this->logger = $logger;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     *
     * @param array $request
     * @param string $entity
     * @param string $requestType
     * @param string $action
     * @return obj
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sync($request, $entity, $requestType, $action = null)
    {
        $devResponse = $this->requestInterface->create();
        try {
            if ($this->cloudHelper->isEnabled()) {
                $requestList = (array)$request;
                $resultData = isset($requestList['ResultData']) ? $requestList['ResultData'] : [];
                if (!empty($resultData)) {
                    $requestList['RequestData'] = (array)$resultData;
                    unset($resultData);

                    $devResponse->setContext(
                        $this->request->create()->prepareContextObject($requestType, $requestList['SchedulerId'])
                    );
                    $apiMethodsRoutes = $this->i95DevRepository->create()->apiServiceMethodRoutes;
                    $apiMethods = $this->getReverseApiMethods($apiMethodsRoutes, 'reverse');

                    if (array_key_exists($entity, $apiMethods)) {
                        return $this->i95DevRepository->create()->serviceMethod(
                            $apiMethods[$entity],
                            $this->jsonHelper->jsonEncode($requestList),
                            $this->cloudHelper->getErpComponent()
                        );
                    }

                }
            }
        } catch (\Exception $e) {
            $this->logger->createLog(
                __METHOD__,
                $action." :: ".$e->getMessage(),
                Logger::EXCEPTION,
                'critical'
            );
        }
    }
}
