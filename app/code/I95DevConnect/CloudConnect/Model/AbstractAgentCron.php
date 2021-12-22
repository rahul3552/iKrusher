<?php

namespace I95DevConnect\CloudConnect\Model;

use \I95DevConnect\CloudConnect\Helper\Data as CloudHelper;
use Magento\Framework\Exception\LocalizedException;
use \I95DevConnect\CloudConnect\Model\LoggerFactory;

/**
 * Class AbstractAgentCron
 * @package I95DevConnect\CloudConnect\Model
 */
abstract class AbstractAgentCron
{
    protected $cloudHelper = null;
    protected $logFilename = '';
    protected $schedulerType = '';
    public $service = null;
    public $logger;

    /**
     * AbstractAgentCron constructor.
     * @param CloudHelper $cloudHelper
     * @param Service $service
     * @param \I95DevConnect\CloudConnect\Model\LoggerFactory $logger
     */
    public function __construct(
        CloudHelper $cloudHelper,
        Service $service,
        LoggerFactory $logger
    ) {
        $this->cloudHelper = $cloudHelper;
        $this->service = $service;
        $this->logger = $logger;
    }

    /**
     * @return bool|string
     */
    protected function startCronProcess()
    {
        try {
            if ($scheduler = $this->getSchedulerDetails()) {
                $this->initiateJob(
                    $scheduler['schedulerId'],
                    $scheduler['schedulerData']
                );
            }
        } catch (LocalizedException $ex) {
            $this->logger->create()->createLog(
                $this->logFilename,
                $ex->getMessage(),
                Logger::EXCEPTION,
                Logger::CRITICAL
            );
            return $ex->getMessage();
        }
        return true;
    }

    /**
     * @param $schedulerId
     * @param $schedulerData
     * @throws LocalizedException
     */
    abstract protected function initiateJob($schedulerId, $schedulerData);

    /**
     * @return array|false
     * @throws LocalizedException
     */
    protected function getSchedulerDetails()
    {
        return $this->cloudHelper->syncData($this->logFilename, $this->schedulerType, $this->service);
    }
}
