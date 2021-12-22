<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95DevConnect\CloudConnect\Model;

use \I95DevConnect\CloudConnect\Api\PushDataInterface;
use \I95DevConnect\CloudConnect\Model\Request;
use \I95DevConnect\MessageQueue\Helper\Data;
use Magento\Framework\Exception\LocalizedException;

/**
 * class to push the data from magento to cloud
 */
class PushData extends AbstractAgentCron implements PushDataInterface
{

    const  SCHEDULER_TYPE = 'PushData';

    /**
     * @var string
     */
    protected $schedulerType = self::SCHEDULER_TYPE;

    /**
     * @var ServiceMethod\ServiceMethodFactory
     */
    public $serviceMethod;

    /**
     * @var int
     */
    public $sendIds;

    /**
     * @var \I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory
     */
    public $requestInterface;

    /**
     * @var \I95DevConnect\CloudConnect\Api\PullResponseInterface
     */
    public $pullResponse;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    public $jsonHelper;
    public $logFilename = self::SCHEDULER_TYPE;
    
    public $pushRecords;
    /**
     * @var \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory
     */
    public $i95DevMagentoMQRepository;

    /**
     * PushData constructor.
     * @param LoggerFactory $logger
     * @param \I95DevConnect\CloudConnect\Model\Request $request
     * @param Service $service
     * @param ServiceMethod\ServiceMethodFactory $serviceMethod
     * @param \I95DevConnect\CloudConnect\Helper\Data $cloudHelper
     * @param \I95DevConnect\MessageQueue\Model\ReadCustomXml $readCustomXml
     * @param \I95DevConnect\CloudConnect\Helper\ConfigHelper $configHelper
     * @param \I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory $requestInterface
     * @param \I95DevConnect\CloudConnect\Api\PullResponseInterface $pullResponse
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param Data $mqHelper
     * @param \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepository
     * @param \Magento\Framework\Event\Manager $eventManager
     */
    public function __construct(
        \I95DevConnect\CloudConnect\Model\LoggerFactory $logger,
        Request $request,
        \I95DevConnect\CloudConnect\Model\Service $service,
        \I95DevConnect\CloudConnect\Model\ServiceMethod\ServiceMethodFactory $serviceMethod,
        \I95DevConnect\CloudConnect\Helper\Data $cloudHelper,
        \I95DevConnect\MessageQueue\Model\ReadCustomXml $readCustomXml,
        \I95DevConnect\CloudConnect\Helper\ConfigHelper $configHelper,
        \I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory $requestInterface,
        \I95DevConnect\CloudConnect\Api\PullResponseInterface $pullResponse,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \I95DevConnect\MessageQueue\Helper\Data $mqHelper,
        \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepository,
        \Magento\Framework\Event\Manager $eventManager
    ) {
        $this->request = $request;
        $this->readCustomXml = $readCustomXml;
        $this->serviceMethod = $serviceMethod;
        $this->configHelper = $configHelper;
        $this->requestInterface = $requestInterface;
        $this->pullResponse = $pullResponse;
        $this->jsonHelper = $jsonHelper;
        $this->mqHelper = $mqHelper;
        $this->i95DevMagentoMQRepository = $i95DevMagentoMQRepository;
        $this->eventManager = $eventManager;
        parent::__construct($cloudHelper, $service, $logger);
    }

    /**
     * {@inheritDoc}
     */
    public function syncData()
    {
        return $this->startCronProcess();
    }

    /**
     * initiate push data job for forward sync
     *
     * @param $schedulerId
     * @param $schedulerData
     * @throws LocalizedException
     */
    protected function initiateJob($schedulerId, $schedulerData)
    {
        try {
            $this->processActiveEntity($schedulerData, $schedulerId);
        } catch (LocalizedException $ex) {
            throw new LocalizedException(__($ex->getMessage()));
        }
    }

    /**
     * @param $schedulerData
     * @param $schedulerId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function entityManagementSync($schedulerData, $schedulerId)
    {
        if ($schedulerData->IsConfigurationUpdated) {
            //@author Janani Allam service call to get entity status
            $schedulerEntityData = $this->service->makeServiceCall(
                self::SCHEDULER_TYPE,
                null,
                null,
                $schedulerId,
                'Entities'
            );
            if ($schedulerEntityData != '') {
                foreach ($schedulerEntityData->ResultData as $entityData) {
                    $this->cloudHelper->updateEntity(
                        $entityData->entityName,
                        $entityData->isOutboundActive,
                        $entityData->isInboundActive,
                        $this->logFilename
                    );
                }
                //@author Janani Allam send ACK for entity Management API
                $this->sendEntityACK(self::SCHEDULER_TYPE, $schedulerId);
            }
        }
    }

    /**
     * Method to push data to cloud
     * @param string $entity
     * @param string $schedulerId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function manipulateEntity($entity, $schedulerId)
    {
        try {
            $this->pushRecords = [];
            $packetSize = $this->cloudHelper->getPacketSize();
            $request = [
                "requestData" => [],
                "packetSize" => $packetSize,
                "erp_name" => 'ERP'
            ];
            $packetSize = $this->cloudHelper->getPacketSize();

            $collection = $this->i95DevMagentoMQRepository->create()->getCollection();
            $collection->addFieldToSelect('msg_id');
            $collection->addFieldToFilter("entity_code", $entity);
            $collection->addFieldToFilter("status", Data::PENDING);
            $collection->getSelect()->order('msg_id', 'ASC');
            $count = (int)($collection->getSize() / $packetSize);
            $loopCount = empty($collection->getSize() % $packetSize) ? $count : ($count + 1);

            while ($loopCount > 0) {
                //fetching entity from magento
                $result = $this->serviceMethod->create()->cloudConnect(
                    json_encode($request),
                    $entity,
                    self::SCHEDULER_TYPE,
                    'getEntityInfo',
                    'sendInfo'
                );

                if (!empty($result) && !empty($result->resultData)) {
                    $this->pushRecords = $result->resultData;
                    $devResponse = $this->requestInterface->create();
                    $devResponse->setContext(
                        $this->request->prepareContextObject(self::SCHEDULER_TYPE, $schedulerId)
                    );
                    $recordData = [];
                    foreach ($result->resultData as $record) {
                        $data = $this->cloudHelper->prepareDataObject();
                        $data->setSourceId($record['sourceId']);
                        $data->setInputData($record['InputData']);
                        $data->setReference($record['reference']);
                        $recordData[] = $data;
                    }
                    if (!empty($recordData)) {
                        $devResponse->setRequestData($recordData);
                        //sending entity to cloud
                        $this->resetRecordsToPending($entity);
                        $pushResponse = $this->service
                            ->makeServiceCall(self::SCHEDULER_TYPE, $entity, $devResponse, $schedulerId);
                        // save response  cloud_msg_id to destination_msg_id
                        $resultData = json_decode($this->jsonHelper->jsonEncode($pushResponse->ResultData), 1);
                        $this->serviceMethod->create()->cloudConnect(
                            $resultData,
                            $entity,
                            self::SCHEDULER_TYPE,
                            'getInfoResponse',
                            'setResponse'
                        );
                    }
                }
                $loopCount--;

            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->create()->createLog(
                'PushDataCron',
                $ex->getMessage(),
                \I95DevConnect\CloudConnect\Model\Logger::EXCEPTION,
                'critical'
            );
            $this->resetRecordsToPending($entity);
        }
    }
    
    /**
     * Method to reset the record status from Request Transferred to Pending on exception
     * @param $entity
     */
    public function resetRecordsToPending($entity)
    {
        try {
            if (!empty($this->pushRecords)) {
                foreach ($this->pushRecords as $record) {
                    $mqRecordCollection = $this->i95DevMagentoMQRepository->create()->getCollection();
                    $mqRecordCollection->addFieldToSelect('msg_id', 'status', 'destination_msg_id')
                        ->addFieldToFilter("msg_id", $record['messageId'])
                        ->addFieldToFilter("entity_code", $entity)
                        ->addFieldToFilter("erp_code", $this->cloudHelper->getErpComponent());
                    $collection = $mqRecordCollection->getSelect()->limit(1);
                    $status =  $mqRecordCollection->getFirstItem()->getStatus();
                    $destinationMessageId =  $mqRecordCollection->getFirstItem()->getDestinationMsgId();
                    if (empty($destinationMessageId) && $status === Data::SUCCESS) {
                        $this->cloudHelper->updateMagentoMQStatus(Data::PENDING, $collection->getData());
                    }
                }
            }
        } catch (LocalizedException $ex) {
            $this->logger->create()->createLog(
                'PushDataCron',
                $ex->getMessage(),
                \I95DevConnect\CloudConnect\Model\Logger::EXCEPTION,
                'critical'
            );
        }
    }

    /**
     * Method to send ACK for Entity status update ACK
     * @param $schedulerType
     * @param $schedulerId
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @author Janani Allam
     */
    public function sendEntityAck($schedulerType, $schedulerId)
    {
        $devReq = $this->requestInterface->create();
        $devReq->setContext(
            $this->request->prepareContextObject('pushData', $schedulerId)
        );
        $devReq->setType('entityUpdate');
        //sending entityAck to cloud
        $result = $this->service
            ->makeServiceCall($schedulerType, null, $devReq, $schedulerId, 'Ack');
        if (!$result->IsConfigurationUpdated) {
            $this->logger->create()->createLog(
                "PushData entity ACK",
                "Entity Management updated in cloud",
                $this->logFilename,
                \I95DevConnect\CloudConnect\Model\Logger::INFO
            );
        }
    }

    /**
     * @param $schedulerData
     * @param $schedulerId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processActiveEntity($schedulerData, $schedulerId)
    {

        $this->entityManagementSync($schedulerData, $schedulerId);

        $this->schedulerData = $schedulerData;

        // @author arushi.bansal dispachted event to support mapping functionality in more modular way
        $this->eventManager->dispatch(
            "push_data_after_subscription_success",
            [
                'currentObject' => $this,
                'schedulerId' => $schedulerId
            ]
        );

        $entities = $this->readCustomXml->getXmlDataOrderBySyncOrder();
        $forwardSkipEntities = $this->configHelper->getForwardSkipEntities();

        foreach ($entities as $entity_code => $entity_value) {
            if (!in_array($entity_code, $forwardSkipEntities)) {
                $this->manipulateEntity($entity_code, $schedulerId);
            }
        }
    }
}
