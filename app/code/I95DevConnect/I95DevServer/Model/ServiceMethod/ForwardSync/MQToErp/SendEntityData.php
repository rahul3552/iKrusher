<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_I95DevServer
 */
namespace I95DevConnect\I95DevServer\Model\ServiceMethod\ForwardSync\MQToErp;

use \I95DevConnect\MessageQueue\Helper\Data as MessageQueueHelper;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class to send entity info/data to ERP constructor
 */
class SendEntityData
{

    public $dataPersistence;
    public $abstractService;
    public $i95DevMagentoMQFactory;
    const MESSAGEID = "messageId";
    const REFERENCE = "reference";

    /**
     * @var object \I95DevConnect\MessageQueue\Model\ErrorUpdateDataFactory
     */
    public $messageErrorModel;

    /**
     * @var \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory
     */
    public $i95DevMagentoMQRepository;

    public $sendId;

    /**
     * @var \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory
     */
    public $logger;

    /**
     * Constructor for DI
     * @param \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepository
     * @param \I95DevConnect\MessageQueue\Api\DataPersistenceInterface $dataPersistence
     * @param \I95DevConnect\I95DevServer\Model\ServiceMethod\ForwardSync\MQToErp\SendIds $sendId
     * @param \I95DevConnect\I95DevServer\Model\ServiceMethod\AbstractServiceMethod $abstractService
     * @param \I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterfaceFactory $i95DevMagentoMQFactory
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param \I95DevConnect\MessageQueue\Model\ErrorUpdateDataFactory $messageErrorModel
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQRepository,
        \I95DevConnect\MessageQueue\Api\DataPersistenceInterface $dataPersistence,
        \I95DevConnect\I95DevServer\Model\ServiceMethod\ForwardSync\MQToErp\SendIds $sendId,
        \I95DevConnect\I95DevServer\Model\ServiceMethod\AbstractServiceMethod $abstractService,
        \I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterfaceFactory $i95DevMagentoMQFactory,
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \I95DevConnect\MessageQueue\Model\ErrorUpdateDataFactory $messageErrorModel
    ) {
        $this->i95DevMagentoMQRepository = $i95DevMagentoMQRepository;
        $this->dataPersistence = $dataPersistence;
        $this->sendId = $sendId;
        $this->abstractService = $abstractService;
        $this->i95DevMagentoMQFactory = $i95DevMagentoMQFactory;
        $this->logger = $logger;
        $this->messageErrorModel = $messageErrorModel;
    }

    /**
     * Method to update record status in outbound MQ
     *
     * @param int $messageId
     * @param int $status
     * @param string $errorMessage
     *
     * @throws LocalizedException
     * @createdBy Sravani Polu
     */
    public function saveRecord($messageId, $status, $errorMessage = '')
    {
        try {
            $i95DevMagentoMQ = $this->i95DevMagentoMQFactory->create();
            if ($errorMessage != '') {
                $message = is_array($errorMessage) ? implode(",", $errorMessage) : $errorMessage;
                $errorDataModel = $this->messageErrorModel->create();
                $errorDataModel->setMsg($message);
                $errorDataModel->save();
                $errorId = $errorDataModel->getId();
                $i95DevMagentoMQ->setErrorId($errorId);
                $this->logger->create()->createLog(
                    __METHOD__,
                    $message,
                    \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                    'error'
                );
            }

            $i95DevMagentoMQ->setMsgId($messageId);
            $i95DevMagentoMQ->setStatus($status);
            $this->i95DevMagentoMQRepository->create()->saveMQData($i95DevMagentoMQ);
        } catch (LocalizedException $ex) {
            $this->logger->create()->createLog(
                __METHOD__,
                $ex->getMessage(),
                \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                'error'
            );
        }
    }

    /**
     * get the entity data
     *
     * @param string $entityCode
     * @param array $dataString
     * @param string $erpName
     * @return array
     * @throws LocalizedException
     */
    public function getEntityData($entityCode, $dataString, $erpName = null)
    {
        $responseData = [];
        foreach ($dataString as $recordRequest) {
            $magentoid = '';
            $messageId = '';
            $reference = null;
            $messageId = $this->setRecordStatusProcessing($recordRequest, $messageId);

            if (isset($recordRequest['magentoId']) && isset($messageId)) {
                $magentoid = $recordRequest['magentoId'];
                $recordData = null;

                $recordMessage = "";
                $processData = $this->processGetEntityInfo($entityCode, $magentoid, $erpName, $messageId);
                $recordData = $processData["recordData"];
                $recordstatus = $processData["recordstatus"];
                $recordMessage = $processData["recordMessage"];
                $reference = $processData[self::REFERENCE];
            } else {
                $recordstatus = false;
                $recordMessage = "Some issue occur. Please contact admin";
            }

            $record['result'] = $recordstatus;
            $record['message'] = $recordMessage;
            if ($messageId !== '') {
                $record[self::MESSAGEID] = $messageId;
                /** @updatedBy Sravani Polu Code starts for updating Outbound MQ record status**/
                $this->setRecordStatusErrorOrSuccess($messageId, $recordstatus, $recordMessage);
                /**Code Ends for updating Outbound MQ record status**/
            }
            if ($magentoid !== '') {
                $record['sourceId'] = $magentoid;
                $record[self::REFERENCE] = $reference;
            }
            $record['InputData'] = $this->abstractService->encryptAES(json_encode($recordData));
            if ($record['result']) {
                $responseData[] = $record;
            }
        }
        return [
            "status" => true,
            "message" => "",
            "responseData" => $responseData
        ];
    }

    /**
     * @param $recordRequest
     * @param $messageId
     * @return mixed|string
     */
    public function setRecordStatusProcessing($recordRequest, $messageId)
    {
        if (isset($recordRequest[self::MESSAGEID])) {
            /** @updatedBy Sravani Polu Added processing status as method parameter **/
            $this->saveRecord($messageId, MessageQueueHelper::PROCESSING);
            return $messageId = $recordRequest[self::MESSAGEID];
        }
        return "";
    }

    /**
     * @param $messageId
     * @param $recordstatus
     * @param $recordMessage
     */
    public function setRecordStatusErrorOrSuccess($messageId, $recordstatus, $recordMessage)
    {
        /** @updatedBy Sravani Polu Code starts for updating Outbound MQ record status**/
        if ($recordstatus) {
            $this->saveRecord($messageId, MessageQueueHelper::SUCCESS);
        } else {
            $this->saveRecord($messageId, MessageQueueHelper::ERROR, $recordMessage);
        }
        /**Code Ends for updating Outbound MQ record status**/
    }

    /**
     * @param $entityCode
     * @param $magentoid
     * @param $erpName
     * @param $messageId
     * @return array
     */
    public function processGetEntityInfo($entityCode, $magentoid, $erpName, $messageId)
    {
        $recordstatus = true;
        $recordMessage = "";
        $reference = null;
        $recordData = [];
        try {
            $recordData = $this->dataPersistence->getEntityInfo(
                $entityCode,
                $magentoid,
                $erpName,
                $messageId
            );

            if (!is_array($recordData)) {
                $recordstatus = false;
                $recordMessage = $recordData;
            } else {
                $reference = array_key_exists(self::REFERENCE, $recordData)? $recordData[self::REFERENCE]:'';
            }
        } catch (LocalizedException $ex) {
            $recordstatus = false;
            $recordMessage = $ex->getMessage();
        }

        return [
            "recordData" => $recordData,
            "recordstatus" => $recordstatus,
            "recordMessage" => $recordMessage,
            "reference" => $reference
        ];
    }
}
