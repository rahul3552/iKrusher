<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95Devconnect\CloudConnect\Model\ServiceMethod\Forward;

use I95Devconnect\CloudConnect\Model\Logger;
use I95DevConnect\CloudConnect\Helper\Data as CloudHelper;

/**
 * Class to sync ERP Response
 */
class ErpResponse
{

    public $i95DevRepository;
    public $cloudHelper;
    public $erpName = 'ERP';
    public $logger;
    public $jsonHelper;
    public $request;
    public $i95DevMagentoMQRepository;

    /**
     * Constructor for DI
     * @param \I95DevConnect\CloudConnect\Helper\Data $cloudHelper
     * @param \I95DevConnect\I95DevServer\Api\I95DevServerRepositoryInterfaceFactory $i95DevRepository
     * @param Logger $logger
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \I95DevConnect\CloudConnect\Model\RequestFactory $request
     * @param \I95DevConnect\CloudConnect\Model\ServiceMethod\ServiceMethodFactory $serviceMethod
     * @param \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepository
     */
    public function __construct(
        \I95DevConnect\CloudConnect\Helper\Data $cloudHelper,
        \I95DevConnect\I95DevServer\Api\I95DevServerRepositoryInterfaceFactory $i95DevRepository,
        Logger $logger,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \I95DevConnect\CloudConnect\Model\RequestFactory $request,
        \I95DevConnect\CloudConnect\Model\ServiceMethod\ServiceMethodFactory $serviceMethod,
        \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepository
    ) {
        $this->cloudHelper = $cloudHelper;
        $this->i95DevRepository = $i95DevRepository;
        $this->logger = $logger;
        $this->jsonHelper = $jsonHelper;
        $this->request = $request;
        $this->serviceMethod = $serviceMethod;
        $this->i95DevMagentoMQRepository = $i95DevMagentoMQRepository;
    }

    /**
     * forward info sync implementation
     * @param $inputs
     * @param string $entity
     * @param string $requestType
     * @param string $action
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sync($inputs, $entity, $requestType, $action = null) // NOSONAR
    {
        try {
            $destinationId = [];
            if ($this->cloudHelper->isEnabled() && (is_array($inputs) && !empty($inputs))) {
                $finalData = [];
                $data = [];
                foreach ($inputs as $input) {
                    $destinationId[] = $input['messageId'];
                    $msg_id = $this->i95DevMagentoMQRepository->create()
                        ->load($input['messageId'], 'destination_msg_id')->getMsgId();
                    if (isset($msg_id) && $msg_id > 0) {
                        $data['inputData'] = $input['inputData'];
                        $data['message'] = $input['message'];
                        $data['messageId'] = $msg_id;
                        $data['targetId'] = $input['targetId'];
                        $data['sourceId'] = $input['sourceId'];
                        $finalData['requestData'][] = $data;
                        $apiMethodsRoutes = $this->i95DevRepository->create()->apiServiceMethodRoutes;
                        $apiMethods = $this->serviceMethod->create()
                            ->getForwardApiMethods($apiMethodsRoutes, 'forward', $action);
                            
                        if (array_key_exists($entity, $apiMethods)) {
                            $this->i95DevRepository->create()->serviceMethod(
                                $apiMethods[$entity],
                                $this->jsonHelper->jsonEncode($finalData),
                                $this->cloudHelper->getErpComponent()
                            );
                        }                                             
                    }
                }
            }
        } catch (\Exception $ex) {
            $this->logger->createLog(
                __METHOD__,
                $ex->getMessage(),
                Logger::EXCEPTION,
                'critical'
            );
        }
        return $destinationId;
    }
}
