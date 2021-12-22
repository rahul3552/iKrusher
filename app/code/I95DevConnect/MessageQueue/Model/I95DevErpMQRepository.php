<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model;

use \Magento\Framework\Model\AbstractModel;
use \I95DevConnect\MessageQueue\Api\I95DevErpMQRepositoryInterface;

/**
 * Data object for ERP Messagequeue
 */
class I95DevErpMQRepository extends AbstractModel implements I95DevErpMQRepositoryInterface
{

    public $logger;
    public $dataObjectProcessor;
    public $i95DevERPDataRepository;
    public $i95DevERPData;

    /**
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \I95DevConnect\MessageQueue\Model\ResourceModel\I95DevErpMQ $resource
     * @param \I95DevConnect\MessageQueue\Model\ResourceModel\I95DevErpMQ\Collection $resourceCollection
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \I95DevConnect\MessageQueue\Api\Data\I95DevErpDataInterfaceFactory $i95DevERPData
     * @param \I95DevConnect\MessageQueue\Api\I95DevErpDataRepositoryInterfaceFactory $i95DevERPDataRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ResourceModel\I95DevErpMQ $resource,
        ResourceModel\I95DevErpMQ\Collection $resourceCollection,
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \I95DevConnect\MessageQueue\Api\Data\I95DevErpDataInterfaceFactory $i95DevERPData,
        \I95DevConnect\MessageQueue\Api\I95DevErpDataRepositoryInterfaceFactory $i95DevERPDataRepository,
        array $data = []
    ) {
        $this->logger = $logger;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->i95DevERPData = $i95DevERPData->create();
        $this->i95DevERPDataRepository = $i95DevERPDataRepository;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     *
     * {@inheritdoc}
     */
    public function saveMQData(\I95DevConnect\MessageQueue\Api\Data\I95DevErpMQInterface $erpMessageQueueData)
    {

        foreach ($erpMessageQueueData->getData() as $attributeCode => $attributeData) {
            $this->setDataUsingMethod($attributeCode, $attributeData);
        }

        $msgId = $erpMessageQueueData->getMsgId();
        if ($msgId) {
            $this->setMsgId($msgId);
        }
        $this->save();

        $dataString = $erpMessageQueueData->getDataString();
        if (isset($dataString)) {
            $this->i95DevERPData->setMsgId($this->getMsgId());
            $this->i95DevERPData->setDataString($dataString);
            $this->i95DevERPDataRepository->create()->saveMQData($this->i95DevERPData);
        }

        return $this;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function get($msgId)
    {
        $messageQueue = $this->load($msgId);

        if ($messageQueue->getMsgId()) {
            $dataStringDetails = $this->i95DevERPDataRepository->create()->getByMsgId($msgId);
            if ($dataStringDetails->getDataId()) {
                $messageQueue->setData('data_id', $dataStringDetails->getDataId());
                $messageQueue->setData('data_string', $dataStringDetails->getDataString());
            }

            return $messageQueue;
        } else {
            $this->logger->create()->createLog(
                __METHOD__,
                'No such entity MsgId : ' . $msgId,
                'general',
                'critical'
            );
            return false;
        }
    }

    /**
     *
     * {@inheritdoc}
     */
    public function deleteMQData($msqId)
    {
        if ($this->load($msqId)) {
            $this->delete();
        }
    }
}
