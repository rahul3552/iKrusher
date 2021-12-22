<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95DevConnect\CloudConnect\Model;

use I95DevConnect\CloudConnect\Api\PullResponseInterface;
use \I95DevConnect\MessageQueue\Helper\Data;
use Magento\Framework\DB\Adapter\LockWaitException;
use Magento\Framework\Exception\LocalizedException;
use I95DevConnect\CloudConnect\Helper\Data as CloudHelper;

/**
 * class to pull the data from cloud to magento
 */
class PullResponse extends AbstractAgentCron implements PullResponseInterface
{
    /**
     * @var string
     */
    public $erpName = "ERP";

    /**
     * @var string
     */
    protected $schedulerType = 'PullResponse';

    /**
     * @var ServiceMethod\ServiceMethodFactory
     */
    public $serviceMethod;

    /**
     * @var \I95DevConnect\CloudConnect\Helper\ConfigHelper
     */
    public $configHelper;

    /**
     * @var string
     */
    public $logFilename = 'PullResponse';

    /**
     * @var \I95DevConnect\MessageQueue\Model\ReadCustomXmlFactory
     */
    public $readCustomXml;

    /**
     * @var \I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory
     */
    public $requestInterface;

    /**
     * @var RequestFactory
     */
    public $request;

    /**
     * @var \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory
     */
    public $i95DevMagentoMQ;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    public $jsonHelper;

    /**
     * @var ServiceMethod\Forward\ResponseAcknowledgement
     */
    public $responseAckSender;

    /**
     * @var Data
     */
    public $mqHelper;

    /**
     * Constructor for DI
     * @param \I95DevConnect\CloudConnect\Model\LoggerFactory $logger
     * @param \I95DevConnect\CloudConnect\Model\Service $service
     * @param \I95DevConnect\CloudConnect\Model\ServiceMethod\ServiceMethodFactory $serviceMethod
     * @param \I95DevConnect\CloudConnect\Helper\ConfigHelper $configHelper
     * @param CloudHelper $cloudHelper
     * @param \I95DevConnect\MessageQueue\Model\ReadCustomXmlFactory $readCustomXml
     * @param \I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory $requestInterface
     * @param \I95DevConnect\CloudConnect\Model\RequestFactory $request
     * @param \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQ
     * @param Data $mqHelper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \I95DevConnect\CloudConnect\Model\ServiceMethod\Forward\ResponseAcknowledgement $responseAckSender
     */
    public function __construct(
        \I95DevConnect\CloudConnect\Model\LoggerFactory $logger,
        \I95DevConnect\CloudConnect\Model\Service $service,
        \I95DevConnect\CloudConnect\Model\ServiceMethod\ServiceMethodFactory $serviceMethod,
        \I95DevConnect\CloudConnect\Helper\ConfigHelper $configHelper,
        CloudHelper $cloudHelper,
        \I95DevConnect\MessageQueue\Model\ReadCustomXmlFactory $readCustomXml,
        \I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory $requestInterface,
        \I95DevConnect\CloudConnect\Model\RequestFactory $request,
        \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQ,
        \I95DevConnect\MessageQueue\Helper\Data $mqHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \I95DevConnect\CloudConnect\Model\ServiceMethod\Forward\ResponseAcknowledgement $responseAckSender
    ) {
        $this->readCustomXml = $readCustomXml;
        $this->serviceMethod = $serviceMethod;
        $this->configHelper = $configHelper;
        $this->requestInterface = $requestInterface;
        $this->request = $request;
        $this->i95DevMagentoMQ = $i95DevMagentoMQ;
        $this->jsonHelper = $jsonHelper;
        $this->responseAckSender = $responseAckSender;
        $this->mqHelper = $mqHelper;
        parent::__construct($cloudHelper, $service, $logger);
    }

    /**
     * {@inheritDoc}
     */
    public function syncResponse()
    {
        return $this->startCronProcess();
    }

    /**
     * Initiate pull data job for reverse sync
     *
     * @param $schedulerId
     * @param $schedulerData
     */
    protected function initiateJob($schedulerId, $schedulerData)
    {
        $this->processActiveSubscription($schedulerId);
    }

    /**
     * Method to pull response to magento from cloud
     * @param string $entity
     * @param string $schedulerId
     */
    public function manipulateEntity($entity, $schedulerId)
    {
        try {
            $destination_msg_id = "destination_msg_id";
            //fetching from magento
            $collection = $this->i95DevMagentoMQ->create()->getCollection()
                ->addFieldToSelect(['msg_id', $destination_msg_id, 'magento_id']);
            $collection->addFieldToFilter("entity_code", $entity);
            $collection->addFieldToFilter("erp_code", $this->cloudHelper->getErpComponent());
            $collection->addFieldToFilter("status", ["in" => [Data::SUCCESS]]);
            $collection->addFieldToFilter($destination_msg_id, ["neq" => null]);
            $collection->getSelect()->order('msg_id', 'ASC');

            if (!empty($collection) && $collection->getSize() > 0) {
                $packetSize = $this->cloudHelper->getPacketSize();
                $devResponse = $this->requestInterface->create();
                $devResponse->setContext(
                    $this->request->create()->prepareContextObject('pullResponse', $schedulerId)
                );
                $packets = array_chunk($collection->getData(), $packetSize);

                $this->processPullResponseRecord($packets, $devResponse, $entity, $schedulerId);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->create()->createLog(
                'PushDataCron',
                $ex->getMessage(),
                \I95DevConnect\CloudConnect\Model\Logger::EXCEPTION,
                'critical'
            );
        }
    }

    /**
     * @param $packets
     * @param $devResponse
     * @param $entity
     * @param $schedulerId
     */
    public function processPullResponseRecord($packets, $devResponse, $entity, $schedulerId)
    {
        $result = null;
        try {
            foreach ($packets as $packet) {
                $recordData = [];
                foreach ($packet as $record) {
                    $data = $this->cloudHelper->prepareDataObject();
                    $data->setSourceId($record['magento_id']);
                    $data->setMessageId((int)$record['destination_msg_id']);
                    $recordData[] = $data;
                }
                if (!empty($recordData)) {
                    $devResponse->setRequestData($recordData);
                    //sending entity to cloud
                    $result = $this->service
                        ->makeServiceCall($this->schedulerType, $entity, $devResponse, $schedulerId);

                    //save target details in OutboundMq and Entities.
                    $destinationId = null;
                    if (is_array($result->ResultData) && !empty($result->ResultData)) {
                        $resultData = json_decode($this->jsonHelper->jsonEncode($result->ResultData), 1);
                        $destinationId = $this->serviceMethod->create()
                            ->cloudConnect(
                                $resultData,
                                $entity,
                                $this->schedulerType,
                                'erpResponse',
                                'setResponse'
                            );
                    } elseif (!empty($result->message)) {
                        $this->logger->createLog(
                            "getting response from magento for magento cloud agent to transfer to cloud",
                            $result->message,
                            $this->logFilename,
                            \I95DevConnect\CloudConnect\Model\Logger::INFO
                        );
                    }                    
                    
                    $this->responseAckSender->syncResponseAck($entity, $destinationId);
                }
            }
        } catch (LocalizedException $ex) {
            throw new LocalizedException(__($ex->getMessage()));
        }
    }

    /**
     * @param $schedulerId
     */
    public function processActiveSubscription($schedulerId)
    {
        try {
            $entities = $this->readCustomXml->create()->getXmlDataOrderBySyncOrder();
            $forwardSkipEntities = $this->configHelper->getForwardSkipEntities();
            foreach ($entities as $entity_code => $entity_value) {
                // Loop all the entities for pulling the data
                if (!in_array($entity_code, $forwardSkipEntities)) {
                    $this->manipulateEntity($entity_code, $schedulerId);
                }
            }
        } catch (LocalizedException $ex) {
            throw new LocalizedException(__($ex->getMessage()));
        }
    }
}
