<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_I95DevServer
 */
namespace I95DevConnect\I95DevServer\Model\ServiceMethod\ForwardSync\MQToErp;

/**
 * Class to Save Entity Response in Outbound MQ
 */
class SendEntityResponse
{
    const MSG = "message";
    const TARGETID = "targetId";
    const SOURCEID = "sourceId";
    const MSGID = "messageId";

    public $i95DevMagentoMQRepository;
    public $dataPersistence;
    public $statusCode = '5';
    public $updatedBy = 'ERP';
    public $i95DevMagentoMQDataFactory;
    public $messageErrorModel;
    public $logger;
    public $messageId;
    public $targetId;

    /**
     * Constructor for DI
     *
     * @param \I95DevConnect\MessageQueue\Api\DataPersistenceInterface $dataPersistence
     * @param \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepository
     * @param \I95DevConnect\MessageQueue\Model\ErrorUpdateDataFactory $messageErrorModel
     * @param \I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterfaceFactory $i95DevMagentoMQDataFactory
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Api\DataPersistenceInterface $dataPersistence,
        \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepository,
        \I95DevConnect\MessageQueue\Model\ErrorUpdateDataFactory $messageErrorModel,
        \I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterfaceFactory $i95DevMagentoMQDataFactory,
	\I95DevConnect\MessageQueue\Model\Logger $logger
    ) {
        $this->dataPersistence = $dataPersistence;
        $this->i95DevMagentoMQRepository = $i95DevMagentoMQRepository;
        $this->messageErrorModel = $messageErrorModel;
        $this->i95DevMagentoMQDataFactory = $i95DevMagentoMQDataFactory;
        $this->logger = $logger;
    }

    /**
     * get the entity data
     *
     * @param string $entityCode
     * @param array $dataString
     * @param string $erpName
     *
     * @return array
     * @throws \Exception
     */
    public function getEntityResponse($entityCode, $dataString, $erpName = null) //NOSONAR
    {
        try {
            $recordData = null;
            $responseData = [];
            $recordstatus = false;
            $recordMessage = "";
            foreach ($dataString as $recordRequest) {
                if (isset($recordRequest[self::MSGID])) {
                    $this->messageId = $recordRequest[self::MSGID];
                    $messageData = $this->i95DevMagentoMQRepository->create()->load($recordRequest[self::MSGID]);

                    $output = $this->prepareRecordStatusNMessage(
                        $messageData,
                        $entityCode,
                        $recordRequest,
                        $erpName
                    );
                    $recordstatus = $output['recordstatus'];
                    $recordMessage = $output['recordMessage'];
                } else {
                    $recordstatus = false;
                    $record[self::MSG] = "MessageId is mandatory";
                }

                /* updatedBy Ranjith; messageId, targetId, sourceId need to be
                set by default irrespective of record sync status */
                $record[self::MSGID] = isset($recordRequest[self::MSGID]) ? $recordRequest[self::MSGID] : null;
                $record[self::TARGETID] = isset($recordRequest[self::TARGETID]) ? $recordRequest[self::TARGETID] : null;
                $record[self::SOURCEID] = isset($recordRequest[self::SOURCEID]) ? $recordRequest[self::SOURCEID] : null;
                $record['result'] = $recordstatus;
                $record[self::MSG] = isset($recordMessage)?$recordMessage:null;
                if (isset($recordData->resultData)) {
                    $record['inputData'] = $recordData->resultData;
                }

                if (!$recordstatus) {
                    $this->updateMessageQueue(
                        $messageData['msg_id'],
                        \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                        $this->updateErrorData($recordMessage)
                    );
                }
                $responseData[] = $record;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(
                __METHOD__,
                $ex->getMessage(),
                \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                'critical'
            );
        }

        return [
            "status" => true,
            self::MSG => "",
            "responseData" => $responseData,
        ];
    }

    /**
     * @param $messageData
     * @param $entityCode
     * @param $recordRequest
     * @param $erpName
     * @return array
     */
    public function prepareRecordStatusNMessage($messageData, $entityCode, $recordRequest, $erpName)
    {
        if (empty($messageData->getData())) {
            $recordstatus = false;
            $recordMessage = "No such message id exist";
        } else {

            if (isset($messageData['entity_code']) && $messageData['entity_code'] != $entityCode) {
                $recordstatus = false;
                $recordMessage = "Either Message id or entity code is wrong";

            } else {
                if (!isset($recordRequest[self::TARGETID]) || $recordRequest[self::TARGETID] =="") {
                    $recordstatus = false;
                    $recordMessage = "No target Id exist because " . $recordRequest[self::MSG];
                } else {
                    $this->targetId = $recordRequest[self::TARGETID];
                    $recordstatus = true;
                    $recordData = $this->dataPersistence->getEntityResponse(
                        $entityCode,
                        json_encode($recordRequest),
                        $erpName
                    );
                    switch ($recordData->getStatus()) {
                        case \I95DevConnect\MessageQueue\Helper\Data::SUCCESS:
			    if($messageData['destination_msg_id'] > 0) {
				$this->statusCode = 6;
			    }
                 	    $this->saveDataInOutboundMQ();	                        
                            $recordMessage = "Erp response set successfully";
                            break;
                        case \I95DevConnect\MessageQueue\Helper\Data::ERROR:
                            $recordstatus = false;
                            $recordMessage = $recordData->getMessage();
                            break;
                        default:
                            $recordstatus = false;
                            $recordMessage = "Something went wrong. Please contact I95Dev team.";
                    }

                }
            }
        }
        return ["recordstatus" => $recordstatus, "recordMessage" => $recordMessage];
    }

    /**
     * @param $msgId
     * @param $status
     * @param $msg
     *
     */
    public function updateMessageQueue($msgId, $status, $msg)
    {
    	$messageData = $this->i95DevMagentoMQDataFactory->create();
        $messageData->setMsgId($msgId);
        if ($status !== null) {
            $messageData->setStatus($status);
        }
        if ($msg !== null) {
            $messageData->setErrorId($msg);
        }
            
        $this->i95DevMagentoMQRepository->create()->saveMQData($messageData);
    }

    /**
     *
     * @param string $message
     *
     * @return int
     * @throws \Exception
     */
    public function updateErrorData($message)
    {
        return $this->dataPersistence->updateErrorData($message);
    }

    public function saveDataInOutboundMQ()
    {
        $i95DevMagentoMQData = $this->i95DevMagentoMQDataFactory->create();
        $i95DevMagentoMQData->setMsgId($this->messageId);
        $i95DevMagentoMQData->setStatus($this->statusCode);
        $i95DevMagentoMQData->setUpdatedby(__($this->updatedBy));
        $i95DevMagentoMQData->setTargetId($this->targetId);
        $this->i95DevMagentoMQRepository->create()->saveMQData($i95DevMagentoMQData);
    }
}
