<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95DevConnect\CloudConnect\Helper;

use I95DevConnect\CloudConnect\Api\Data\DataInterfaceFactory;
use I95DevConnect\CloudConnect\Model\Logger;
use I95DevConnect\I95DevServer\Model\ServiceMethod\ForwardSync\MQToErp\SendEntityResponse;
use I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory;
use I95DevConnect\MessageQueue\Model\ResourceModel\EntityFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Cloud connect class for common functionality
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const SUCCESS_C = 6;

    public $scopeConfig;
    public $dataInterface;
    public $erpName = 'ERP';
    public $entityTypeModel;
    public $logger;
    public $sendEntityResponse;
    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $mqHelper;
    /**
     * @var EntityResponse
     */
    public $entityResource;
    /**
     * @var StoreManagerInterface
     */
    public $storeManager;
    
    public $i95DevMagentoMQRepository;

    /**
     * Constructor for DI
     * @param ScopeConfigInterface $scopeConfig
     * @param DataInterfaceFactory $dataInterface
     * @param \I95DevConnect\MessageQueue\Helper\Data $mqHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \I95DevConnect\MessageQueue\Model\EntityFactory $entityTypeModel
     * @param EntityFactory $entityResource
     * @param Logger $logger
     * @param SendEntityResponse $sendEntityResponse
     * @param I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepository
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        DataInterfaceFactory $dataInterface,
        \I95DevConnect\MessageQueue\Helper\Data $mqHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \I95DevConnect\MessageQueue\Model\EntityFactory $entityTypeModel,
        EntityFactory $entityResource,
        Logger $logger,
        SendEntityResponse $sendEntityResponse,
        I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepository
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->dataInterface = $dataInterface;
        $this->mqHelper = $mqHelper;
        $this->entityTypeModel = $entityTypeModel;
        $this->entityResource = $entityResource;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->sendEntityResponse = $sendEntityResponse;
        $this->i95DevMagentoMQRepository = $i95DevMagentoMQRepository;
    }

    /**
     * get cloud connector status
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(
            'i95dev_adapter_configurations/enabled_disabled/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );
    }

    /**
     * get client id from magento configuration
     * @return string
     */
    public function getConfigClientId()
    {
        return $this->scopeConfig->getValue(
            'i95dev_adapter_configurations/enabled_disabled/client_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );
    }

    /**
     * get subscription key from magento configuration
     * @return string
     */
    public function getConfigSubscriptionKey()
    {
        return $this->scopeConfig->getValue(
            'i95dev_adapter_configurations/enabled_disabled/subscription_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );
    }

    /**
     * get Endpoint code from magento configuration
     * @return string
     */
    public function getConfigEndpointCode()
    {
        return $this->scopeConfig->getValue(
            'i95dev_adapter_configurations/enabled_disabled/endpoint_code',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );
    }

    /**
     * get ApiAuthenticationToken from magento configuration
     * @return string
     */
    public function getApiAuthenticationToken()
    {
        return $this->scopeConfig->getValue(
            'i95dev_adapter_configurations/enabled_disabled/token',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );
    }

    /**
     * get target url from magento configuration
     * @return string
     */
    public function getTargetUrl()
    {
        return $this->scopeConfig->getValue(
            'i95dev_adapter_configurations/enabled_disabled/target_url',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );
    }

    /**
     * get erp component
     * @return string
     */
    public function getErpComponent()
    {
        $erpConfigName = $this->scopeConfig->getValue(
            'i95dev_messagequeue/I95DevConnect_settings/component',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );

        if (!$erpConfigName) {
            $erpConfigName = __($this->erpName);
        }

        return $erpConfigName;
    }

    /**
     * get packet size
     * @return int
     */
    public function getPacketSize()
    {
        return $this->mqHelper->getPacketSize();
    }

    /**
     * get instance type
     * @return string
     */
    public function getInstanceType()
    {
        return $this->scopeConfig->getValue(
            'i95dev_adapter_configurations/enabled_disabled/instance_type',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );
    }

    /**
     * prepare data object
     * @return object
     */
    public function prepareDataObject()
    {
        return $this->dataInterface->create();
    }

    /**
     * update entity in/outbound flags
     * @param string $entityName
     * @param bool $outboundFlag
     * @param bool $inboundFlag
     * @param string $fileName
     * @return boolean
     * @author Arushi.Bansal
     */
    public function updateEntity($entityName, $outboundFlag, $inboundFlag, $fileName)
    {
        $entityCollectionModel = $this->entityTypeModel->create()->getCollection()->addFieldToSelect('entity_code');
        $entityCollectionModel->addFieldToFilter('entity_code', $entityName);
        if ($entityCollectionModel->getSize() > 0) {
            foreach ($entityCollectionModel as $entity) {
                $entityResourceModel= $this->entityResource->create();
                $entityModel = $this->entityTypeModel->create()->load($entity->getEntityCode());
                $entityModel->setSupportForInbound($outboundFlag);
                $entityModel->setSupportForOutbound($inboundFlag);
                $entityResourceModel->save($entityModel);

                if ($entity->getEntityCode() == 'Customer') {
                    $entityModel = $this->entityTypeModel->create()->load('address');
                    $entityModel->setSupportForInbound($outboundFlag);
                    $entityModel->setSupportForOutbound($inboundFlag);
                    $entityModel->save();
                }
            }
        }
        return true;
    }

    /**
     * @param $logFilename
     * @param $schedulerType
     * @param $service
     * @return array
     */
    public function syncData($logFilename, $schedulerType, $service)
    {
        if ($this->isEnabled()) {
            $schedulerData = $service->makeServiceCall($schedulerType);
            if ($schedulerData != '' && $schedulerData->Result) {
                $isSubscriptionActive = $schedulerData->IsSubscriptionActive;
                if (!$isSubscriptionActive) {
                    throw new LocalizedException(
                        __("It seems that your subscription is expired")
                    );
                } else {
                    $schedulerId = $schedulerData->SchedulerId;
                    return [
                        "schedulerId" => $schedulerId,
                        "schedulerData" => $schedulerData
                    ];
                }
            } else {
                throw new LocalizedException(
                    __("No Schedular id generated")
                );
            }
        } else {
            throw new LocalizedException(
                __("I95Dev Cloud Connector is disabled")
            );
        }
    }

    /**
     * Method to update the magento outbound message queue record status
     * @param $status
     * @param $records
     */
    public function updateMagentoMQStatus($status, $msg_id)
    {
        try {
            if (empty($msg_id)) {
                return false;
            }

            $messageData = $this->i95DevMagentoMQRepository->create()->load($msg_id);
            if ($messageData) {
                $messageDetails = $messageData->getData();
                if ($messageDetails['status'] != 3) {

                    $this->sendEntityResponse->updateMessageQueue($msg_id, $status, '');
                }                                
            }            
        } catch (LocalizedException $ex) {
            $this->logger->create()->createLog(
                'PullResponseAckCron',
                $ex->getMessage(),
                Logger::EXCEPTION,
                'critical'
            );
        }
    }
}
