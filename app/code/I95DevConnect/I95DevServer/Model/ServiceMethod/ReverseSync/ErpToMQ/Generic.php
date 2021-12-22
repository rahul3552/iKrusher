<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_I95DevServer
 */
namespace I95DevConnect\I95DevServer\Model\ServiceMethod\ReverseSync\ErpToMQ;

/**
 * Class to get reverse entity from ERP and save in to Inbound MQ
 */
class Generic
{
    const DESTINATIONID= "DestinationId";
    public $logger;
    public $i95DevErpMQFactory;
    public $date;
    public $i95DevErpMQRepository;
    public $dataPersistence;
    public $currententitiyCode;
    public $erpName;
    public $parentData;

    /**
     * Constructor for DI
     * @param \I95DevConnect\MessageQueue\Model\I95DevErpMQRepositoryFactory $i95DevErpMQRepository
     * @param \I95DevConnect\MessageQueue\Api\Data\I95DevErpMQInterfaceFactory $i95DevErpMQFactory
     * @param \I95DevConnect\MessageQueue\Model\Logger $logger
     * @param \I95DevConnect\MessageQueue\Api\DataPersistenceInterfaceFactory $dataPersistence
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Model\I95DevErpMQRepositoryFactory $i95DevErpMQRepository,
        \I95DevConnect\MessageQueue\Api\Data\I95DevErpMQInterfaceFactory $i95DevErpMQFactory,
        \I95DevConnect\MessageQueue\Model\Logger $logger,
        \I95DevConnect\MessageQueue\Api\DataPersistenceInterfaceFactory $dataPersistence,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->i95DevErpMQFactory = $i95DevErpMQFactory;
        $this->i95DevErpMQRepository = $i95DevErpMQRepository;
        $this->logger = $logger;
        $this->date = $date;
        $this->dataPersistence = $dataPersistence;
    }

    /**
     * Insert data in to Inbound message queue
     *
     * @param array $recordData
     * @param \I95DevConnect\I95DevServer\Model\ServiceMethod\ReverseSync $reverseSync
     *
     * @return type
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function defaultMessageQueueInsert($recordData, $reverseSync)
    {
        try {
            $targetKey = \I95DevConnect\MessageQueue\Helper\Data::TARGET_KEY;
            $referenceKey = \I95DevConnect\MessageQueue\Helper\Data::REF_KEY;
            $targetId = $this->checkTargetId($recordData, $targetKey);
            $recordData[$referenceKey] = $referKey = $this->checkReferenceKey($recordData, $referenceKey);
            $messageId = null;

            $i95DevErpMQ = $this->i95DevErpMQFactory->create();
            $i95DevErpMQ->setErpCode($reverseSync->erpName);
            $i95DevErpMQ->setEntityCode($reverseSync->currententitiyCode);
            $i95DevErpMQ->setTargetId($targetId);
            $i95DevErpMQ->setCreatedDt($this->date->gmtDate());
            $i95DevErpMQ->setStatus(\I95DevConnect\MessageQueue\Helper\Data::PENDING);
            $i95DevErpMQ->setRefName($referKey);
            $i95DevErpMQ->setUpdatedDt($this->date->gmtDate());
            $this->setI95DevErpMQValue($recordData, $i95DevErpMQ, $targetId);

            $i95DevErpMQ->setDataString(json_encode($recordData));
            /* Updated by Ranjith Rasakatla, Null value check for parent data */
            if (isset($reverseSync->currentMethodProperties['isChild']) &&
                $reverseSync->parentData !== null) {
                $i95DevErpMQ->setParentMsgId($reverseSync->parentData->getMessageId());
            }

            $mqData = $this->i95DevErpMQRepository->create()->saveMQData($i95DevErpMQ);
            $messageId = $mqData->getMsgId();
            //@ Hrusikesh added extra parameter $messageId
            if (isset($reverseSync->currentMethodProperties['saveWithSync']) &&
                $messageId && json_encode($recordData)) {
                $response = $this->dataPersistence->create()->createEntity(
                    $reverseSync->currententitiyCode,
                    json_encode($recordData),
                    $messageId
                );

                if (isset($response)) {
                    $this->dataPersistence->create()->updateErpMQStatus(
                        $response->getStatus(),
                        $response->getResultdata(),
                        $response->getMessage(),
                        $messageId
                    );
                    if ($response->status == \I95DevConnect\MessageQueue\Helper\Data::ERROR) {
                        return ['messageId' => $messageId, 'message' => $response->getMessage()];
                    }
                } else {
                    $this->dataPersistence->create()->updateErpMQStatus(
                        \I95DevConnect\MessageQueue\Helper\Data::ERROR,
                        null,
                        'Some issue occur while saving data. Please contact admin.',
                        $messageId
                    );
                }
            }
            return $messageId;
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(
                __METHOD__,
                $ex->getMessage(),
                \I95DevConnect\MessageQueue\Helper\Data::I95EXC,
                'critical'
            );

            return $ex->getMessage();
        }
    }

    /**
     * @param $recordData
     * @param $targetKey
     * @return mixed
     */
    public function checkTargetId($recordData, $targetKey)
    {
        if (isset($recordData[$targetKey])) {
            return $recordData[\I95DevConnect\MessageQueue\Helper\Data::TARGET_KEY];
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__("target_id_required"));
        }
    }

    /**
     * @param $recordData
     * @param $referenceKey
     * @return mixed|null
     */
    public function checkReferenceKey($recordData, $referenceKey)
    {
        if (isset($recordData[$referenceKey])) {
            return $recordData[\I95DevConnect\MessageQueue\Helper\Data::REF_KEY];
        } else {

            return null;
        }
    }

    /**
     * @param $recordData
     * @param $i95DevErpMQ
     * @param $targetId
     */
    public function setI95DevErpMQValue($recordData, &$i95DevErpMQ, $targetId)
    {
        if (isset($recordData[self::DESTINATIONID]) && $recordData[self::DESTINATIONID] > 0) {
            $i95DevErpMQ->setDestinationMsgId($recordData[self::DESTINATIONID]);
            $record_exists = $this->i95DevErpMQRepository->create()->getCollection()
                ->addFieldToSelect(['msg_id','status'])
                ->addFieldToFilter("destination_msg_id", $recordData[self::DESTINATIONID])
                ->addFieldToFilter("target_id", $targetId)
                ->getFirstItem();
            if (!empty($record_exists->getData()) &&
                $record_exists->getStatus() == \I95DevConnect\MessageQueue\Helper\Data::ERROR) {
                $i95DevErpMQ->setStatus(\I95DevConnect\MessageQueue\Helper\Data::ERROR);
                $i95DevErpMQ->setMsgId($record_exists->getMsgId());
            } elseif (!empty($record_exists->getData())) {
                $i95DevErpMQ->setMsgId($record_exists->getMsgId());
            }
        }
    }
}
