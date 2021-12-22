<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    CreditLimit
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Model\ResourceModel;

use Aheadworks\CreditLimit\Api\Data\JobInterface;
use Aheadworks\CreditLimit\Model\Source\Job\Status as JobStatus;

/**
 * Class Job
 *
 * @package Aheadworks\CreditLimit\Model\ResourceModel
 */
class Job extends AbstractResourceModel
{
    /**
     * Main table name
     */
    const MAIN_TABLE_NAME = 'aw_cl_job';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, JobInterface::ID);
    }

    /**
     * Save new job
     *
     * @param array $jobData
     * @throws \Exception
     * @return bool
     */
    public function saveJob($jobData)
    {
        $connection = $this->transactionManager->start($this->getConnection());
        try {
            if (isset($jobData[JobInterface::ID])) {
                $connection->update(
                    $this->getMainTable(),
                    $jobData,
                    [JobInterface::ID . ' = (?)' => $jobData[JobInterface::ID]]
                );
            } else {
                $connection->insert($this->getMainTable(), $jobData);
            }
            $this->transactionManager->commit();
        } catch (\Exception $e) {
            $this->transactionManager->rollBack();
            throw $e;
        }

        return true;
    }

    /**
     * Get all jobs ready to run
     *
     * @throws \Exception
     * @return array
     */
    public function getReadyToRunJobs()
    {
        $connection = $this->getConnection();
        $select = $connection
            ->select()
            ->from($this->getMainTable())
            ->where(JobInterface::STATUS . '=?', JobStatus::READY)
            ->order(JobInterface::ID . ' ASC');

        return $connection->fetchAll($select);
    }
}
