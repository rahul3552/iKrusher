<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model;

use \I95DevConnect\MessageQueue\Api\LoggerInterface;

/**
 * Class for log creation
 */
class Logger implements LoggerInterface
{
    const MAX_LOG_SIZE = 5000;

    public $logger;
    public $zendLogger;
    public $fileStream;
    public $ioOperations;
    public $path = "/var/log/i95dev/";
    protected $fileDriver;

    /**
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public $date;

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
        $this->date = $date;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->fileDriver = $fileDriver;
    }

    /**
     * Creates log folder
     *
     * @param boolean $date
     *
     * @throws \Exception
     */
    public function createLogFolder($date = null)
    {
        if ($date) {
            // reverted code for log creation using magento framework
            $this->ioOperations->checkAndCreateFolder(BP . $this->path . date('Y-m-d'), 0777);
        } else {
            // reverted code for log creation using magento framework
            $this->ioOperations->checkAndCreateFolder(BP . $this->path, 0777);
        }
    }

    /**
     * Get Absolute path of the log file to be created.
     *
     * @param string $logName
     *
     * @return string $logPath
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function getLogPath($logName)
    {
        try {
            if ($logName == 'general') {
                $this->createLogFolder(false);
                $logPath = BP . $this->path . $logName;
                return $this->recurciveFileCheck(1, $logPath);
            } else {
                $this->createLogFolder(true);
                $todayDate = $this->date->gmtDate();
                $dateObj = new \DateTime($todayDate);
                $logPath = BP . $this->path . $dateObj->format('Y-m-d') . '/' . $logName;
                return $this->recurciveFileCheck(1, $logPath);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }

    /**
     *
     * {@inheritdoc}
     */
    public function createLog($logArea, $message, $logName, $logType)
    {
        try {
            //@author Divya Koona. Checking logs configuration enabled or not.
            $logsEnabled = $this->scopeConfig->getValue(
                'i95dev_messagequeue/I95DevConnect_logsettings/debug',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                $this->storeManager->getDefaultStoreView()->getWebsiteId()
            );
            if (!$logsEnabled) {
                return;
            }

            $logsTypeEnabled = $this->scopeConfig->getValue(
                'i95dev_messagequeue/I95DevConnect_logsettings/logtype',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                $this->storeManager->getDefaultStoreView()->getWebsiteId()
            );
            $logsTypeArray = explode(",",$logsTypeEnabled);

            // to prevent multiple logging
            $this->logger = new \Zend_Log();
            $logPath = $this->getLogPath($logName);

            $writer = new \Zend_Log_Writer_Stream($logPath, 'a', null, 0777);
            $this->logger->addWriter($writer);

            if (is_array($message) || is_object($message)) {
                $message = json_encode($message);
            }

            switch ($logType) {
                case "info":
                if (in_array("info", $logsTypeArray)){
                    $this->logger->info($logArea);
                    $this->logger->info($message);
                }
                    break;
                case "critical":
                if (in_array("critical", $logsTypeArray)){
                    $this->logger->crit($logArea);
                    $this->logger->crit($message);
                }
                    break;
                case "error":
                if (in_array("error", $logsTypeArray)){
                    $this->logger->err($logArea);
                    $this->logger->err($message);
                }
                    break;
                default:
                if (in_array("debug", $logsTypeArray)){
                    $this->logger->debug($logArea);
                    $this->logger->debug($message);
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }

    /**
     * Recursively check the file already exists or not.
     * If exist will check if it reached to maximum size.
     * If it has reached to max size it will create a new file with one increment count.
     *
     * @param int $count
     * @param string $logPath
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return string $logPath
     */
    public function recurciveFileCheck($count, $logPath)
    {
        try {
            $checkPath = $logPath . '_' . $count . '.log';

            if ($this->fileDriver->isExists($checkPath)) {
                /* @updatedBy Ranjith Rasakatla, get file size change as earlier
                 * used file function popen() was taking more memory, processing
                 * and file was not closed after the functionality utilization */
                // phpcs:disable
                $fsize = filesize($checkPath);
                // phpcs:enable
                $maxConfigFileSize = $this->scopeConfig->getValue(
                    'i95dev_messagequeue/I95DevConnect_logsettings/max_log_size',
                    \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                    $this->storeManager->getDefaultStoreView()->getWebsiteId()
                );

                /* @updatedBy Ranjith Rasakatla, converting size KB to B for comparison */
                $maxFileSize = ((isset($maxConfigFileSize) && trim($maxConfigFileSize)) ?
                        $maxConfigFileSize : self::MAX_LOG_SIZE) * 1024;
                if ($fsize > $maxFileSize) {
                    $logPath = $this->recurciveFileCheck($count + 1, $logPath);
                    if ($logPath) {
                        return $logPath;
                    }
                } else {
                    return $checkPath;
                }
            } else {
                return $checkPath;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }
}
