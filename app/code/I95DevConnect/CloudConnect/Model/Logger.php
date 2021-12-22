<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95DevConnect\CloudConnect\Model;

/**
 * Class for log management
 */
class Logger extends \I95DevConnect\MessageQueue\Model\Logger
{

    const PUSHDATA = 'PushData';
    const PUSHRESPONSE = 'PushResponse';
    const PULLRESPONSE = 'PullResponse';
    const PULLDATA = 'PullData';
    const PULLRESPONSEACK = 'PullResponseAck';
    const GENERIC = 'Generic';
    const EXCEPTION = 'I95DevCloudException';
    const CLEANDATA = 'I95DevCloudCleanData';
    const CLEANDATAEXCEPTION = 'I95DevCloudCleanDataException';
    const CRITICAL = 'critical';
    const INFO = 'info';

    public $path = "/var/log/i95dev/cloud/";

    /**
     *
     * @param \Magento\Framework\Filesystem\Io\File $ioOperations
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     */
    public function __construct(
        \Magento\Framework\Filesystem\Io\File $ioOperations,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem\Driver\File $fileDriver
    ) {
        $this->ioOperations = $ioOperations;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->fileDriver = $fileDriver;
        parent::__construct($ioOperations, $date, $scopeConfig, $storeManager, $fileDriver);
    }

    /**
     * get log file type/name
     * @param string $schedulerType
     * @param string $entity
     * @return string
     */
    public function getEntityLogType($schedulerType, $entity = null)
    {
        if ($schedulerType) {
            $schedulerType = strtoupper($schedulerType);
            $logType = constant("self::{$schedulerType}");
        } else {
            return $entity . Logger::GENERIC;
        }

        return $entity . $logType;
    }

    /**
     * create cloud log folder
     * @param type $date
     * @throws \Exception
     */
    public function createLogFolder($date = null)
    {
        try {
            parent::createLogFolder($date);
            if ($date) {
                $this->ioOperations->checkAndCreateFolder(BP . "/var/log/i95dev/cloud/" . date('Y-m-d'), 0777);
            } else {
                $this->ioOperations->checkAndCreateFolder(BP . "/var/log/i95dev/cloud", 0777);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * create log using MessageQueue
     * @param string $logArea
     * @param string $message
     * @param string $logName
     * @param string $logType
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createLog($logArea, $message, $logName, $logType)
    {
        if ($this->isLogsEnabled()) {
            parent::createLog($logArea, $message, $logName, $logType);
        }
    }

    /**
     * get cloud connector status
     *
     * @return boolean
     */
    public function isLogsEnabled()
    {
        return $this->scopeConfig->getValue(
            'i95dev_adapter_configurations/enabled_disabled/logs_enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );
    }
}
