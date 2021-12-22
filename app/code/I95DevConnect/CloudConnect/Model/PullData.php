<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95DevConnect\CloudConnect\Model;

use I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory;
use \I95DevConnect\CloudConnect\Api\PullDataInterface;
use I95DevConnect\CloudConnect\Api\PushResponseInterface;
use I95DevConnect\CloudConnect\Helper\ConfigHelper;
use I95DevConnect\CloudConnect\Model\ServiceMethod\ServiceMethodFactory;
use \I95DevConnect\MessageQueue\Helper\Data as MqHelper;
use \I95DevConnect\CloudConnect\Helper\Data;
use I95DevConnect\MessageQueue\Model\ReadCustomXml;
use Magento\Framework\Exception\LocalizedException;

/**
 * class to pull the data from cloud to magento
 */
class PullData extends AbstractAgentCron implements PullDataInterface
{

    public $erpName = "ERP";

    /**
     * @var string
     */
    protected $schedulerType = 'PullData';

    /**
     * @var ServiceMethodFactory
     */
    public $serviceMethod;

    /**
     * @var ConfigHelper
     */
    public $configHelper;
    protected $logFilename = 'PullData';

    /**
     * @var ReadCustomXml
     */
    public $readCustomXml;

    /**
     * @var RequestInterfaceFactory
     */
    public $requestInterface;

    /**
     * @var RequestFactory
     */
    public $request;

    /**
     * @var PushResponseInterface
     */
    public $pushResponse;

    /**
     * @var \Magento\Framework\Event\Manager
     */
    public $eventManager;

    /**
     * PullData constructor.
     * @param LoggerFactory $logger
     * @param Service $service
     * @param ServiceMethodFactory $serviceMethod
     * @param ConfigHelper $configHelper
     * @param Data $cloudHelper
     * @param ReadCustomXml $readCustomXml
     * @param RequestInterfaceFactory $requestInterface
     * @param RequestFactory $request
     * @param PushResponseInterface $pushResponse
     * @param \Magento\Framework\Event\Manager $eventManager
     */
    public function __construct(
        \I95DevConnect\CloudConnect\Model\LoggerFactory $logger,
        Service $service,
        ServiceMethodFactory $serviceMethod,
        ConfigHelper $configHelper,
        Data $cloudHelper,
        ReadCustomXml $readCustomXml,
        RequestInterfaceFactory $requestInterface,
        RequestFactory $request,
        PushResponseInterface $pushResponse,
        \Magento\Framework\Event\Manager $eventManager
    ) {
        $this->readCustomXml = $readCustomXml;
        $this->serviceMethod = $serviceMethod;
        $this->configHelper = $configHelper;
        $this->requestInterface = $requestInterface;
        $this->request = $request;
        $this->pushResponse = $pushResponse;
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
     * initiate pull data job for reverse sync
     *
     * @param $schedulerId
     * @param $schedulerData
     */
    protected function initiateJob($schedulerId, $schedulerData)
    {
        try {
            // Get the Scheduler Id - that will send with every request response cycle with cloud
            if ($schedulerData->IsConfigurationUpdated) {
                //@author Janani Allam service call for getting entity data info
                $schedulerEntityData = $this->service->makeServiceCall(
                    $this->schedulerType,
                    null,
                    null,
                    $schedulerId,
                    'Entities'
                );

                $this->updateEntity($schedulerEntityData, $schedulerId);
            }

            $this->schedulerData = $schedulerData;

            // @author arushi.bansal dispachted event to support mapping functionality in more modular way
            $this->eventManager->dispatch(
                "pull_data_after_subscription_success",
                [
                    'currentObject' => $this,
                    'schedulerId' => $schedulerId
                ]
            );

            $entities = $this->getEntityBySyncOrder();

            $this->processPullDataFunctionality($entities, $schedulerId);
        } catch (LocalizedException $ex) {
            throw new LocalizedException(__($ex->getMessage()));
        }
    }

    /**
     * update entity
     *
     * @param $schedulerEntityData
     * @param $schedulerId
     */
    public function updateEntity($schedulerEntityData, $schedulerId)
    {

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
            $this->sendEntityACK($this->schedulerType, $schedulerId);
        }
    }

    /**
     * fetch the data from cloud
     * @param $entities
     * @param $schedulerId
     */
    public function processPullDataFunctionality($entities, $schedulerId)
    {
        $reverseSkipEntities = $this->configHelper->getReverseSkipEntities();

        foreach ($entities as $entity_code => $entity_value) {

            // Loop all the entities for pulling the data
            if (!in_array($entity_code, $reverseSkipEntities)) {
                do {
                    $requestObj = $this->requestInterface->create();
                    $packetSize = $this->cloudHelper->getPacketSize();
                    $requestObj->setContext(
                        $this->request->create()->prepareContextObject(
                            $this->schedulerType,
                            $schedulerId
                        )
                    );
                    $requestObj->setPacketSize($packetSize);

                    // fetch the data from cloud
                    $data = $this->service->makeServiceCall(
                        $this->schedulerType,
                        $entity_code,
                        $requestObj,
                        $schedulerId
                    );

                    if (!empty($data->ResultData) && is_array($data->ResultData)) {
                        // process the fetched data and move to cloud
                        $this->serviceMethod->create()->cloudConnect(
                            $data,
                            $entity_code,
                            $this->schedulerType,
                            'reverse'
                        );
                    }
                } while (!empty($data->ResultData));
            }
        }
    }

    /**
     * Method to get sync order for all entities
     * @return array
     * @throws LocalizedException
     */
    public function getEntityBySyncOrder()
    {
        return $this->readCustomXml->getXmlDataOrderBySyncOrder();
    }

    /**
     * Method to send ACK for Entity status update
     * @param $schedulerType
     * @param $schedulerId
     * @return boolean
     */
    public function sendEntityAck($schedulerType, $schedulerId)
    {
        $devReq = $this->requestInterface->create();
        $devReq->setContext(
            $this->request->create()->prepareContextObject('pullData', $schedulerId)
        );
        $devReq->setType('entityUpdate');
        //sending entityAck to cloud
        $result = $this->service
            ->makeServiceCall($schedulerType, null, $devReq, $schedulerId, 'Ack');
        if (!$result->IsConfigurationUpdated) {
            $this->logger->create()->createLog(
                "PullData entity ACK",
                "Entity Management updated in cloud",
                $this->logFilename,
                \I95DevConnect\CloudConnect\Model\Logger::INFO
            );
        }
        return true;
    }
}
