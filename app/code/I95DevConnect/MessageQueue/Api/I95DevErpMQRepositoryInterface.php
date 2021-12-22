<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Api;

/**
 * Repository Interface for Erp MessageQueue.
 */
interface I95DevErpMQRepositoryInterface
{

    /**
     * Save erp message queue data
     *
     * @param Data\I95DevErpMQInterface $erpMessageQueueData
     * @return $this
     */
    public function saveMQData(\I95DevConnect\MessageQueue\Api\Data\I95DevErpMQInterface $erpMessageQueueData);

    /**
     * Return erp message queue data. In case msqId not found exception will be thrown.
     *
     * @param int $msqId
     * @return \I95DevConnect\MessageQueue\Api\Data\I95DevErpMQInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($msqId);

    /**
     * Delete erp message queue data
     * If error occurred during the delete exception will be thrown.
     *
     * @param string $msqId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteMQData($msqId);
}
