<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model;

use I95DevConnect\MessageQueue\Api\LoggerInterface;

/**
 * Class responsible for cleaning logs and message queue data
 */
class DataClean
{
    const LOG_CLEAN_DAYS = 30;
    const I95DEV='i95dev';
    const CLOUD='cloud';
    const EXCEP='i95devException';

    /**
     *
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    public $directoryList;

    /**
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public $date;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $msgHelper;
    public $logger;
    public $scopeConfig;
    public $storeManager;
    protected $fileDriver;

    /**
     *
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \I95DevConnect\MessageQueue\Helper\Data $msgHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \I95DevConnect\MessageQueue\Helper\Data $msgHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        LoggerInterface $logger
    ) {
        $this->directoryList = $directoryList;
        $this->date = $date;
        $this->msgHelper = $msgHelper;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->fileDriver = $fileDriver;
    }

    /**
     * Cleans all old data, like old logs, old message queue data
     *
     * @return void
     * @throws \Exception
     * @throws \Exception
     */
    public function cleanData()
    {
        $this->logClean();
        $this->cleanMQData();
        $this->cleanMagentoMQData();
    }

    /**
     * Cleans old logs
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     * @author Divya Koona. Updated code to clean logs before configured days
     * and zip last day log before configured days
     */
    public function logClean()
    {
        $path = $this->directoryList->getPath('log');
        $days = $this->scopeConfig->getValue(
            'i95dev_messagequeue/I95DevConnect_logsettings/log_clean_days',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );

        $zipDays = $this->scopeConfig->getValue(
            'i95dev_messagequeue/I95DevConnect_logsettings/log_zip_days',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );

        if (!trim($days)) {
            $days = self::LOG_CLEAN_DAYS;
        }

        $toDeleteDate = $this->getLastDateFromDays($days);
        $toZipDate = $this->getLastDateFromDays($zipDays);

        $isCloudEnabled = $this->scopeConfig->getValue(
            'i95dev_adapter_configurations/enabled_disabled/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );

        try {
            if ($isCloudEnabled) {
                $dir = $path . DS . self::I95DEV . DS . self::CLOUD;
            } else {
                $dir = $path . DS . self::I95DEV;
            }
            // phpcs:disable
            $cDir = scandir($dir);
            // phpcs:enable

            $this->archiveLogDirectory($cDir, $toZipDate, $isCloudEnabled, $path);

            // phpcs:disable
            $currentDir = scandir($dir);
            // phpcs:enable

            $this->deleteLogDirectory($currentDir, $toDeleteDate, $isCloudEnabled, $path);
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(__METHOD__, $ex->getMessage(), self::EXCEP, LoggerInterface::CRITICAL);
        }
    }

    /**
     * @param $cdir
     * @param $toDeleteDate
     * @param $isCloudEnabled
     * @param $path
     */
    public function deleteLogDirectory($cdir, $toDeleteDate, $isCloudEnabled, $path)
    {
        foreach ($cdir as $value) {
            if (!in_array($value, [".", ".."]) && $value < $toDeleteDate) {
                if ($isCloudEnabled) {
                    $logCleaner = $path . DS . self::I95DEV . DS . self::CLOUD . DS . $value;
                } else {
                    $logCleaner = $path . DS . self::I95DEV . DS . $value;
                }
                if ($this->fileDriver->isDirectory($logCleaner)) {
                    $this->deleteDirectory($logCleaner);
                }
                if ($this->fileDriver->isExists($logCleaner)) {
                    $this->fileDriver->deleteFile($logCleaner);
                }
            }
        }
    }

    /**
     * @param $cdir
     * @param $toDeleteDate
     * @param $isCloudEnabled
     * @param $path
     */
    public function archiveLogDirectory($cdir, $toZipDate, $isCloudEnabled, $path)
    {
        foreach ($cdir as $value) {
            if (!in_array($value, [".", ".."]) && $value < $toZipDate) {
                if ($isCloudEnabled) {
                    $logArchive = $path . DS . self::I95DEV . DS . self::CLOUD . DS . $value;
                } else {
                    $logArchive = $path . DS . self::I95DEV . DS . $value;
                }
                if ($this->fileDriver->isDirectory($logArchive)) {
                    $this->compressLog($logArchive);
                }
            }
        }
    }

    /**
     * Compress previous day log file.
     *
     * @param string $filePath
     */
    public function compressLog($filePath)
    {
        try {
            if (!class_exists('\ZipArchive')) {
                $this->logger->createLog(
                    __METHOD__,
                    __('ZipArchive class not found'),
                    self::EXCEP,
                    LoggerInterface::CRITICAL
                );
            }
            // phpcs:disable
            chdir($filePath);
            // phpcs:enable
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($filePath),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );
            $zip = new \ZipArchive();
            $zip->open($filePath . '.zip', \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $realPath = $file->getRealPath();
                    $relativePath = substr($realPath, strlen($realPath) + 1);
                    $zip->addFile($realPath, $relativePath);
                }
            }
            $zip->close();
            $this->deleteDirectory($filePath);
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(__METHOD__, $ex->getMessage(), self::EXCEP, LoggerInterface::CRITICAL);
        }
    }

    /**
     * @param $dir
     * @return bool
     */
    public function deleteDirectory($dir)
    {
        if ($this->dirExists($dir) && $this->isDirectory($dir)) {
            // phpcs:disable
            foreach (scandir($dir) as $item) {
                // phpcs:enable
                if ($item == '.' || $item == '..') {
                    continue;
                }

                if (!$this->deleteDirectory($dir . DS . $item)) {
                    return false;
                }
            }

            return $this->fileDriver->deleteDirectory($dir);
        }
        return true;
    }

    /**
     * Checks if if given directory is exists or not
     *
     * @param string $dir
     * @return boolean
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function dirExists($dir)
    {
        if ($this->fileDriver->isExists($dir)) {
            return true;
        }
    }

    /**
     * Checks if the given path is a directory or not
     *
     * @param string $dir
     * @return boolean
     */
    private function isDirectory($dir)
    {
        if (!$this->fileDriver->isDirectory($dir)) {
            $this->fileDriver->deleteFile($dir);
            return false;
        }

        return true;
    }

    /**
     * clean messagequeue data with status complete and error with limit 5
     *
     * return boolean
     */
    public function cleanMQData()
    {
        try {
            $syncEntities = $this->msgHelper->getEntityTypeList();

            if (!empty($syncEntities)) {
                //@author Divya Koona. Sending entity code instead of entity name to clean MQ data
                foreach ($syncEntities as $entityCode => $entityName) {
                    $status = $this->msgHelper->deleteMQData($entityCode);
                    if (!$status) {
                        $this->logger->createLog(
                            __METHOD__,
                            "An error occured while deleting records from MQ for entity " . $entityCode,
                            self::EXCEP,
                            LoggerInterface::CRITICAL
                        );
                    }
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(__METHOD__, $ex->getMessage(), self::EXCEP, LoggerInterface::CRITICAL);
        }
        return true;
    }

    /**
     * clean messagequeue data with status complete and error with limit 5
     *
     * return boolean
     */
    public function cleanMagentoMQData()
    {
        try {
            $syncEntities = $this->msgHelper->getEntityTypeList();
            if (!empty($syncEntities)) {
                //@author Divya Koona. Sending entity code instead of entity name to clean MQ data
                foreach ($syncEntities as $entityCode => $entityName) {
                    $status = $this->msgHelper->deleteMMQData($entityCode);
                    if (!$status) {
                        $this->logger->createLog(
                            __METHOD__,
                            "An error occured while deleting records from MQ for entity " . $entityCode,
                            self::EXCEP,
                            LoggerInterface::CRITICAL
                        );
                    }
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(__METHOD__, $ex->getMessage(), self::EXCEP, LoggerInterface::CRITICAL);
        }
        return true;
    }

    /**
     * get last date by days provided from today
     * @param int $days
     * @return string
     */
    public function getLastDateFromDays($days)
    {
        $todayDate = $this->date->gmtDate();
        $dateObj = new \DateTime($todayDate);
        $dateObj->modify('-' . $days . ' day');
        return $dateObj->format('Y-m-d');
    }
}
