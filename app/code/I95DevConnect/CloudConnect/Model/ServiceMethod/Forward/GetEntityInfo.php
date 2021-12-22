<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95Devconnect\CloudConnect\Model\ServiceMethod\Forward;

use I95Devconnect\CloudConnect\Model\Logger;

/**
 * Class to get Entity wise Information
 */
class GetEntityInfo
{

    private $i95DevRepository;
    public $cloudHelper;
    public $erpName = 'ERP';
    public $logger;
    public $jsonHelper;
    public $request;

    /**
     *
     * @param \I95DevConnect\CloudConnect\Helper\Data $cloudHelper
     * @param \I95DevConnect\I95DevServer\Api\I95DevServerRepositoryInterfaceFactory $i95DevRepository
     * @param Logger $logger
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \I95DevConnect\CloudConnect\Model\RequestFactory $request
     * @param \I95DevConnect\CloudConnect\Model\ServiceMethod\ServiceMethodFactory $serviceMethod
     */
    public function __construct(
        \I95DevConnect\CloudConnect\Helper\Data $cloudHelper,
        \I95DevConnect\I95DevServer\Api\I95DevServerRepositoryInterfaceFactory $i95DevRepository,
        Logger $logger,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \I95DevConnect\CloudConnect\Model\RequestFactory $request,
        \I95DevConnect\CloudConnect\Model\ServiceMethod\ServiceMethodFactory $serviceMethod
    ) {
        $this->cloudHelper = $cloudHelper;
        $this->i95DevRepository = $i95DevRepository;
        $this->logger = $logger;
        $this->jsonHelper = $jsonHelper;
        $this->request = $request;
        $this->serviceMethod = $serviceMethod;
    }

    /**
     *
     * @param type $input
     * @param type $entity
     * @param $requestType
     * @param type $action
     * @return I95DevConnect\MessageQueue\Model\I95DevResponse|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sync($input, $entity, $requestType = null, $action = null) //NOSONAR
    {
        $dataList = null;
        try {
            if ($this->cloudHelper->isEnabled()) {
                $apiMethodsRoutes = $this->i95DevRepository->create()->apiServiceMethodRoutes;
                $apiMethods = $this->serviceMethod->create()
                    ->getForwardApiMethods($apiMethodsRoutes, 'forward', $action);
                if (array_key_exists($entity, $apiMethods)) {
                    $dataList = $this->i95DevRepository->create()->serviceMethod(
                        $apiMethods[$entity],
                        $input,
                        $this->cloudHelper->getErpComponent()
                    );
                }
            }
        } catch (\Exception $e) {
            $this->logger->createLog(
                __METHOD__,
                $e->getMessage(),
                Logger::EXCEPTION,
                'critical'
            );
        }
        return $dataList;
    }
}
