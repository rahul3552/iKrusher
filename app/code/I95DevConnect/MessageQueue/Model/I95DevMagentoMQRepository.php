<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model;

use \Magento\Framework\Model\AbstractModel;
use \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterface;

/**
 * Data object for Magento Messagequeue
 */
class I95DevMagentoMQRepository extends AbstractModel implements I95DevMagentoMQRepositoryInterface
{

    public $logger;
    public $dataObjectProcessor;
    public $i95DevMagentoMQRepository;

    /**
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \I95DevConnect\MessageQueue\Model\ResourceModel\I95DevMagentoMQ $resource
     * @param \I95DevConnect\MessageQueue\Model\ResourceModel\I95DevMagentoMQ\Collection $resourceCollection
     * @param \I95DevConnect\MessageQueue\Model\Logger $logger
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \I95DevConnect\MessageQueue\Model\ResourceModel\I95DevMagentoMQ $resource,
        \I95DevConnect\MessageQueue\Model\ResourceModel\I95DevMagentoMQ\Collection $resourceCollection,
        \I95DevConnect\MessageQueue\Model\Logger $logger,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        array $data = []
    ) {
        $this->logger = $logger;
        $this->dataObjectProcessor = $dataObjectProcessor;

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
    public function saveMQData(\I95DevConnect\MessageQueue\Api\Data\I95DevMagentoMQInterface $magentoMQData)
    {
        try {
            $datetime = date('Y-m-d H:i:s');
            $magentoMQData->setUpdatedDt($datetime);

            if (empty($magentoMQData->getMsgId())) {
                $magentoMessageQueueCollection = $this
                        ->getCollection()
                        ->addFieldToFilter("erp_code", $magentoMQData->getErpCode())
                        ->addFieldToFilter("entity_code", $magentoMQData->getEntitycode())
                        ->addFieldToFilter('magento_id', $magentoMQData->getMagentoId())
                        ->addFieldToFilter('status', ['eq'=>1])
                        ->addFieldToSelect('msg_id');

                if ($magentoMessageQueueCollection->getSize() > 0) {
                    foreach ($magentoMessageQueueCollection as $collection) {
                        $magentoMQData->setMsgId($collection->getMsgId());
                    }
                }
            }
            if ($magentoMQData->getMsgId() == "" || $magentoMQData->getMsgId() < 1) {
                $magentoMQData->setCreatedDt($datetime);
            }

            foreach ($magentoMQData->getData() as $attributeCode => $attributeData) {
                $this->setDataUsingMethod($attributeCode, $attributeData);
            }

            $this->save();
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }

        return $this;
    }

    /**
     *
     * {@inheritdoc}
     */
    public function get($msgId)
    {
        $message_queue = $this->load($msgId);

        if ($message_queue->getMsgId()) {
            return $message_queue;
        } else {
            $this->logger->createLog(
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
