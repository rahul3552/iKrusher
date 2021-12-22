<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95DevConnect\CloudConnect\Model;

use \I95DevConnect\MessageQueue\Api\LoggerInterface;

/**
 * Model class for MQ Data and Logs Clean
 */
class DataClean
{

    const MAX_DAYS = '7';

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
    protected $fileDriver;

    /**
     * Constructor for DI
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \I95DevConnect\MessageQueue\Helper\Data $msgHelper
     * @param \Magento\Framework\Filesystem\DriverInterface $driverSystem
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \I95DevConnect\MessageQueue\Helper\Data $msgHelper,
        \Magento\Framework\Filesystem\DriverInterface $driverSystem,
        LoggerInterface $logger,
        \Magento\Framework\Filesystem\Driver\File $fileDriver
    ) {
        $this->directoryList = $directoryList;
        $this->date = $date;
        $this->msgHelper = $msgHelper;
        $this->logger = $logger;
        $this->driverSystem = $driverSystem;
        $this->fileDriver = $fileDriver;
    }

    /**
     * Method to get no. of days to clean
     * @return int
     */
    public function getLogCleanDays()
    {
        $logData = $this->getLogsData();
        $logMaxData = $logData['logs']['maxdays'];
        return ($logMaxData ? $logMaxData : self::MAX_DAYS);
    }

    /**
     * Method to clean log files
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function logClean()
    {
        $path = $this->directoryList->getPath('log');
        $todayDate = $this->date->gmtDate();
        $days = $this->getLogCleanDays();
        $dateObj = new \DateTime($todayDate);
        $dateObj->modify('-' . $days . ' day');
        $toDeleteDate = $dateObj->format('Y-m-d');

        try {
            for ($i = $days; $i > 0; $i--) {
                $dateObj->modify('-1 day');
                $toDeleteDate = $dateObj->format('Y-m-d');

                $logCleaner = $path . DS . 'i95dev' . DS . 'cloud' . DS . $toDeleteDate;
                if ($this->driverSystem->isDirectory($logCleaner)) {
                    $this->deleteDirectory($logCleaner);
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }

    /**
     * Method to delete empty directory
     * @param string $dir
     * @return boolean
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function deleteDirectory($dir)
    {
        if ($this->dirExists($dir) && $this->isDirectory($dir)) {
            // phpcs:disable
            foreach (scandir($dir) as $item) {
                //phpcs:enable
                if ($item == '.' || $item == '..') {
                    continue;
                }

                if (!$this->deleteDirectory($dir . DS . $item)) {
                    return false;
                }
            }

            return $this->driverSystem->deleteDirectory($dir);
        }
        return true;
    }

    /**
     * Method to check directory exist or not
     * @param string $dir
     * @return boolean
     */
    public function dirExists($dir)
    {
        if ($this->fileDriver->isExists($dir)) {
            return true;
        }
    }

    /**
     * Method to check directory and remove if it is not
     * @param string $dir
     * @return boolean
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function isDirectory($dir)
    {
        if (!$this->driverSystem->isDirectory($dir)) {
            // phpcs:disable
            unlink($dir);
            // phpcs:enable
            return false;
        }
        return true;
    }

    /**
     * get log data from setting.xml file
     *
     * @return array
     */
    public function getLogsData()
    {
        return $this->msgHelper->readXml(
            'etc',
            'I95DevConnect_MessageQueue',
            'settings.xml',
            'I95DevConnect_MessageQueue'
        );
    }
}
