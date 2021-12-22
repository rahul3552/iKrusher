<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Api;

/**
 * Repository Interface for i95dev_erp_data.
 */
interface I95DevErpDataRepositoryInterface
{

    /**
     * Save erp message queue data string
     *
     * @param \I95DevConnect\MessageQueue\Api\Data\I95DevErpDataInterface $erpData
     * @return \I95DevConnect\MessageQueue\Api\Data\I95DevErpDataInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return $this
     */
    public function saveMQData(\I95DevConnect\MessageQueue\Api\Data\I95DevErpDataInterface $erpData);

    /**
     * Return erp message queue data string. In case msqId not found exception will be thrown.
     *
     * @param string $msgId
     * @return \I95DevConnect\MessageQueue\Api\Data\I95DevErpDataInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($msgId);

    /**
     * Delete erp message queue data string
     * If error occurred during the delete exception will be thrown.
     *
     * @param int $dataId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteMQData($dataId);
}
