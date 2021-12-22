<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model;

use \Magento\Framework\Model\AbstractModel;
use \I95DevConnect\MessageQueue\Api\I95DevErpDataRepositoryInterface;

/**
 * Data object for ERP data
 */
class I95DevErpDataRepository extends AbstractModel implements I95DevErpDataRepositoryInterface
{

    public $logger;

    /**
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \I95DevConnect\MessageQueue\Model\ResourceModel\I95DevErpData $resource
     * @param \I95DevConnect\MessageQueue\Model\ResourceModel\I95DevErpData\Collection $resourceCollection
     * @param \I95DevConnect\MessageQueue\Model\Logger $logger
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \I95DevConnect\MessageQueue\Model\ResourceModel\I95DevErpData $resource,
        \I95DevConnect\MessageQueue\Model\ResourceModel\I95DevErpData\Collection $resourceCollection,
        \I95DevConnect\MessageQueue\Model\Logger $logger,
        array $data = []
    ) {
        $this->logger = $logger;
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
    public function saveMQData(\I95DevConnect\MessageQueue\Api\Data\I95DevErpDataInterface $erpData)
    {
        foreach ($erpData->getData() as $attributeCode => $attributeData) {
            $this->setDataUsingMethod($attributeCode, $attributeData);
        }
        $msgId = $erpData->getMsgId();
        if ($msgId) {
            $checkData = $this->getByMsgId($msgId);
            if (!empty($checkData)) {
                $this->setDataId($checkData->getDataId());
            }

            $this->save();
        }
        return $this;
    }

    /**
     * get data string of erp message queue on basis of msgId
     * @param int $msgId
     * @return Object
     * @throws \NoSuchEntityException
     */
    public function getByMsgId($msgId)
    {
        $erpData = $this->load($msgId, 'msg_id');
        if ($erpData->getDataId()) {
            return $erpData;
        } else {
            return $this;
        }
    }

    /**
     * Return erp message queue data. In case msgId not found exception will be thrown.
     *
     * @param string $dataId
     * @return \I95DevConnect\MessageQueue\Api\Data\I95DevErpMQInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($dataId)
    {
        $erpData = $this->load($dataId);

        if ($erpData->getDataId()) {
            return $erpData;
        } else {
            return $this;
        }
    }

    /**
     * Delete erp message queue data
     * If error occurred during the delete exception will be thrown.
     *
     * @param int $dataId
     *
     * @throws \Exception
     */
    public function deleteMQData($dataId)
    {
        if ($this->load($dataId)) {
            $this->delete();
        }
    }
}
