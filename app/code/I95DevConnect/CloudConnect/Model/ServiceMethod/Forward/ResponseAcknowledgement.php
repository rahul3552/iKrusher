<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95DevConnect\CloudConnect\Model\ServiceMethod\Forward;

use I95DevConnect\MessageQueue\Helper\Data;
use I95DevConnect\CloudConnect\Helper\Data as CloudHelper;

/**
 * Class to send Acknowledgement for ERP Response
 */
class ResponseAcknowledgement
{
    public $logger;
    public $schedulerType = 'PullResponseAck';
    public $service;
    public $cloudHelper;
    public $requestInterface;
    public $request;
    public $i95DevMagentoMQ;

    /**
     * Constructor for DI
     * @param \I95DevConnect\CloudConnect\Model\LoggerFactory $logger
     * @param \I95DevConnect\CloudConnect\Model\Service $service
     * @param CloudHelper $cloudHelper
     * @param \I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory $requestInterface
     * @param \I95DevConnect\CloudConnect\Model\RequestFactory $request
     * @param \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQ
     */
    public function __construct(
        \I95DevConnect\CloudConnect\Model\LoggerFactory $logger,
        \I95DevConnect\CloudConnect\Model\Service $service,
        CloudHelper $cloudHelper,
        \I95DevConnect\CloudConnect\Api\Data\RequestInterfaceFactory $requestInterface,
        \I95DevConnect\CloudConnect\Model\RequestFactory $request,
        \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQ
    ) {
        $this->logger = $logger;
        $this->service = $service;
        $this->cloudHelper = $cloudHelper;
        $this->requestInterface = $requestInterface;
        $this->request = $request;
        $this->i95DevMagentoMQ = $i95DevMagentoMQ;
    }

    /**
     * Method to send Acknowledgement to ERP
     * @param array $destinationId
     * @param string $entity
     */
    public function syncResponseAck($entity, $destinationId)
    {
        try {
            $schedulerData = $this->service->makeServiceCall($this->schedulerType);
            $destination_msg_id = 'destination_msg_id';
            if ($schedulerData != '' && $schedulerData->Result) {
                $isSubscriptionActive = $schedulerData->IsSubscriptionActive;
                $schedulerId = $schedulerData->SchedulerId;
                if ($isSubscriptionActive) {                          
                    $collection = $this->i95DevMagentoMQ->create()->getCollection();
                    $collection->addFieldToSelect(['msg_id','magento_id', $destination_msg_id]);
                    $collection->addFieldToFilter("entity_code", $entity);
                    $collection->addFieldToFilter("erp_code", $this->cloudHelper->getErpComponent());
                    $collection->addFieldToFilter("status", ["in" => [CloudHelper::SUCCESS_C]]);
                    $collection->addFieldToFilter($destination_msg_id, ["in" => $destinationId]);
                    $collection->getSelect()->order('msg_id', 'ASC');

                    if (!empty($collection) && $collection->getSize() > 0) {
                        $packetSize = $this->cloudHelper->getPacketSize();
                        $devResponse = $this->requestInterface->create();
                        $devResponse->setContext(
                            $this->request->create()->prepareContextObject($this->schedulerType, $schedulerId)
                        );
                        $packets = array_chunk($collection->getData(), $packetSize);

                        $this->sendEntityToCloud($packets, $entity, $devResponse, $schedulerId);
                    }
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->create()->createLog(
                'PullResponseAckCron',
                $ex->getMessage(),
                \I95DevConnect\CloudConnect\Model\Logger::EXCEPTION,
                'critical'
            );
        }
    }

    /**
     * send entity to cloud
     * @param $packets
     * @param $entity
     * @param $devResponse
     * @param $schedulerId
     */
    public function sendEntityToCloud($packets, $entity, $devResponse, $schedulerId)
    {
        try {
            $ackResponse = null;
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
                    $ackResponse = $this->service
                        ->makeServiceCall($this->schedulerType, $entity, $devResponse, $schedulerId);

                }
                
            	if (is_object($ackResponse) && $ackResponse->Result) {
            	
              foreach($packet as $record) {
                          $this->logger->create()->createLog(
                'PullResponseAckCron',
		$record["msg_id"],
		"responselog",
                'critical'
            );
                	$this->cloudHelper->
                	updateMagentoMQStatus(Data::COMPLETE, $record["msg_id"]);
        	}
            	}
            }

            
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->create()->createLog(
                'PullResponseAckCron',
                $ex->getMessage(),
                \I95DevConnect\CloudConnect\Model\Logger::EXCEPTION,
                'critical'
            );
            throw new LocalizedException(__($ex->getMessage()));
        }
    }
}
