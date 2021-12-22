<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_ErrorData
 */

namespace I95DevConnect\ErrorData\Model;

use \I95DevConnect\ErrorData\Helper\Generic;
use \I95DevConnect\MessageQueue\Helper\Data as MqDataHelper;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;

/*
 * Model Class for Sending the Error Report to the Customer
 * @author Ranjith R
*/

/**
 * Class ScheduledReport for sending scheduled report.
 */
class ScheduledReport
{
    const MAGENTO = "magento";
    const NOTIFICATIONSENT = "notification_sent";
    const MSGID = "msg_id";
    const TARGETID = "target_id";

    public $directory_list;
    public $errorData;
    public $email;
    public $errorMsgData;
    public $genericHelper;
    public $logger;
    public $header = [
        'Message Id',
        'Entity',
        'Magento Id',
        'ERP Id',
        'Reference',
        'Created Date',
        'Updated Date',
        'Error Message'
    ];
    public $filePointer = null;
    public $notificationList = [];
    public $notificationMsgList = [];
    public $driverInterface;

    /**
     * @var MqDataHelper
     */
    public $mqHelper;

    /**
     *
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directory_list
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param \I95DevConnect\ErrorData\Model\ErrorDataFactory $errorData
     * @param \I95DevConnect\ErrorData\Model\ErrorMessageDataFactory $errorMsgData
     * @param Generic $genericHelper
     * @param \I95DevConnect\ErrorData\Model\Email $email
     * @param \Magento\Framework\Filesystem\Driver\File $driverInterface
     * @param MqDataHelper $mqHelper
     */
    public function __construct(
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \I95DevConnect\ErrorData\Model\ErrorDataFactory $errorData,
        \I95DevConnect\ErrorData\Model\ErrorMessageDataFactory $errorMsgData,
        Generic $genericHelper,
        \I95DevConnect\ErrorData\Model\Email $email,
        \Magento\Framework\Filesystem\Driver\File $driverInterface,
        MqDataHelper $mqHelper
    ) {
        $this->directory_list = $directory_list;
        $this->logger = $logger;
        $this->errorData = $errorData;
        $this->errorMsgData = $errorMsgData;
        $this->genericHelper = $genericHelper;
        $this->email = $email;
        $this->driverInterface = $driverInterface;
        $this->mqHelper = $mqHelper;
    }

    /**
     * Get the list of errors and Send report to the customer
     * @author Ranjith R
     */
    public function sendReport()
    {
        try {
            if ($this->genericHelper->isEnabled()) {
                $this->sendErpToMagentoErrors();
            }
        } catch (\Exception $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), Generic::I95REPORT, Generic::CRITICAL);
        }
    }

    /**
     * Send ERP to eCommerce syncing error report in mail at schedule time.
     */
    public function sendErpToMagentoErrors()
    {
        $inboundEntities = $this->genericHelper->getEnabledEntities();
        if (empty($inboundEntities)) {
            return;
        }

        $directoryName = "MQErrorReport";
        foreach ($inboundEntities as $entityCode) {
            $ibMqErrorList = $this->getEntityWiseErrorRecords('erp', $entityCode);
            if (!empty($ibMqErrorList)) {
                $fileName = $directoryName . $entityCode .  date('Y-m-d') . '.csv';
                $titleERP2M = "List of $entityCode records has failed to synchronize from ERP to eCommerse";
                $this->writeFile($this->header, $titleERP2M, $directoryName, $ibMqErrorList, $fileName);
                $emailSent = $this->sendEmail($directoryName, $entityCode, $ibMqErrorList);
                if ($emailSent) {
                    $this->updateNotification($emailSent);
                    $fileToDelete = $directoryName . DS . $fileName;
                    $this->deleteReportFile($fileToDelete);
                }

                $this->filePointer = null;
            }
        }
    }

    public function sendMagentoToErpError()
    {
        $directoryName = "MQErrorReport";
        $obMqErrorList = $this->getMQErrorRecords(self::MAGENTO);
        $titleM2ERP = "List of records whose respective Entity has failed in creation from Magento to ERP";
        $this->writeFile($this->header, $titleM2ERP, $directoryName, $obMqErrorList);
        $emailSent = $this->sendEmail($directoryName);
        $this->updateNotification($emailSent);
    }

    /**
     * Get Message Queue Records which has errors
     * @param string $origin
     * @return array $mqList
     * @author Ranjith R
     */
    public function getMQErrorRecords($origin)
    {
        try {
            $this->notificationMsgList[$origin] = [];
            $errorLogRec = $this->errorData->create()->getCollection()
                ->addFieldToFilter(self::NOTIFICATIONSENT, [['neq' => 1], ['null' => true]])
                ->addFieldToFilter('origin', $origin)
                ->getData();
            if (empty($errorLogRec)) {
                return $this->notificationMsgList[$origin];
            }

            foreach ($errorLogRec as $errorLogRecData) {
                $errorMsgList = $this->errorMsgData->create()->getCollection()
                    ->addFieldToFilter("notification_id", $errorLogRecData['id'])
                    ->getData();
                if (empty($errorMsgList)) {
                    continue;
                }
                $this->notificationList[$origin][] = $errorLogRecData[self::MSGID];
                $this->getMessageList($errorLogRecData, $errorMsgList, $origin);
            }
        } catch (LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), Generic::I95REPORT, Generic::CRITICAL);
        }

        return $this->notificationMsgList[$origin];
    }

    /**
     * Prepare entity wise error report.
     *
     * @param string $origin
     * @param string $entityCode
     * @return array
     */
    public function getEntityWiseErrorRecords($origin, $entityCode)
    {
        try {
            $this->notificationMsgList[$origin] = [];
            $errorLogRec = $this->errorData->create()->getCollection()
                ->addFieldToFilter(self::NOTIFICATIONSENT, [['neq' => 1], ['null' => true]])
                ->addFieldToFilter('origin', $origin)
                ->addFieldToFilter('entity_code', $entityCode)
                ->getData();
            if (empty($errorLogRec)) {
                return $this->notificationMsgList[$origin];
            }

            foreach ($errorLogRec as $errorLogRecData) {
                $errorMsgList = $this->errorMsgData->create()->getCollection()
                    ->addFieldToFilter("notification_id", $errorLogRecData['id'])
                    ->getData();
                if (empty($errorMsgList)) {
                    continue;
                }
                $this->notificationList[$origin][] = $errorLogRecData[self::MSGID];
                $this->getMessageList($errorLogRecData, $errorMsgList, $origin);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), Generic::I95REPORT, Generic::CRITICAL);
        }

        return $this->notificationMsgList[$origin];
    }

    /**
     * Get the list of error messages
     *
     * @param array $errorLogRecData
     * @param array $errorMsgList
     * @param string $origin
     * @author Ranjith R
     */
    public function getMessageList($errorLogRecData, $errorMsgList, $origin)
    {
        try {
            if ($origin == "erp") {
                $mqData = $this->genericHelper->getIMQData($errorLogRecData[self::MSGID]);
            } else {
                $mqData = $this->genericHelper->getOMQData($errorLogRecData[self::MSGID]);
            }

            if ($mqData) {
                foreach ($errorMsgList as $errorMsgData) {
                    $this->notificationMsgList[$origin][] = [
                        self::MSGID => $mqData->getMsgId(),
                        "entity_code" => $mqData->getEntityCode(),
                        "magento_id" => $mqData->getMagentoId(),
                        self::TARGETID => $mqData->getTargetId(),
                        "reference" =>$mqData->getRefName(),
                        "created_at" => $errorLogRecData['created_at'],
                        "updated_at" => $errorLogRecData['updated_at'],
                        "message" => $errorMsgData['message']
                    ];
                }
            }
        } catch (LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), Generic::I95REPORT, Generic::CRITICAL);
        }
    }

    /**
     * Write the error data to the file
     *
     * @param string $header
     * @param string $title
     * @param string $directoryName
     * @param array $mqErrorList
     * @param string $fileName
     *
     * @author Ranjith R
     */
    public function writeFile($header, $title, $directoryName, $mqErrorList, $fileName)
    {
        try {
            if (!$this->filePointer && !empty($mqErrorList)) {
                $this->filePointer = $this->getFile($directoryName, $fileName);
            }

            if ($this->filePointer && !empty($mqErrorList)) {
                fputcsv($this->filePointer, [$title], ",");
                fputcsv($this->filePointer, $header, ",");
                foreach ($mqErrorList as $data) {
                    fputcsv($this->filePointer, $data, ",");
                }
                fputcsv($this->filePointer, [""], ",");
            }
        } catch (LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), Generic::I95REPORT, Generic::CRITICAL);
        }
    }

    /**
     * Create a file for writing the error data
     *
     * @param string $name
     * @param string $fileName
     *
     * @return resource|null
     * @author Ranjith R
     */
    public function getFile($name, $fileName)
    {
        try {
            $directory = $this->directory_list->getPath('var') . DS . "log" . DS . $name;
            if (!$this->driverInterface->isDirectory($directory)) {
                $this->driverInterface->createDirectory($directory, 0755);
            }

            return $this->driverInterface->fileOpen($directory . DS . $fileName, 'w+');
        } catch (LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), Generic::I95REPORT, Generic::CRITICAL);
            return null;
        }
    }

    /**
     * Send error report as an email to customer
     *
     * @param string $directoryName
     * @param string $entityCode
     * @param array $ibMqErrorList
     *
     * @return boolean
     * @author Ranjith R
     */
    public function sendEmail($directoryName, $entityCode, $ibMqErrorList)
    {
        try {
            if (empty($this->notificationList)) {
                throw new \Magento\Framework\Exception\LocalizedException('notificationList empty');
            }
            if ($this->filePointer) {
                $directory = $this->directory_list->getPath('var') . DS . "log" . DS . $directoryName;
                $this->driverInterface->fileClose($this->filePointer);
                $file = $directory . DS . $directoryName . $entityCode.  date('Y-m-d') . '.csv';
                $mailDetails = $this->genericHelper->getContactDetails();
                $template = 'i95devconnect_error_report';
                $subject = "i95Dev Notification System | ";
                $subject .= "Error in $entityCode Sync from ERP to eCommerce";
                $message = $this->getMessage($entityCode, $ibMqErrorList);
                return $this->email->sendEmail($file, $mailDetails, $template, $subject, $message);
            }
        } catch (LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), Generic::I95REPORT, Generic::CRITICAL);
            return false;
        }
        return false;
    }

    /**
     * Get the message body for the email
     *
     * @param string $entityCode
     * @param array $ibMqErrorList
     *
     * @return string $message
     * @author Ranjith R
     */
    public function getMessage($entityCode, $ibMqErrorList)
    {
        $message = "<br/><p>There are errors in $entityCode Sync from ERP to eCommerce.";
        switch ($entityCode) {
            case 'address':
                $message .= "<br/>The following $entityCode ID's of respective Customer has error :</p>";
                break;
            case 'inventory':
                $message .= "<br/>Inventory sync for the following SKU's has error :</p>";
                break;
            case 'product':
                $message .= "<br/>The following product SKU's has error :</p>";
                break;
            default:
                $message .= "<br/>The following $entityCode ID's has error :</p>";

        }

        $i = 1;
        $errorIds = [];
        foreach ($ibMqErrorList as $error) {
            if (in_array($error[self::TARGETID], $errorIds)) {
                continue;
            }
            array_push($errorIds, $error[self::TARGETID]);
            if ($entityCode === 'address') {
                $message .= "<br>($i) " . "Address Id:: "
                    . $error[self::TARGETID]. " Customer Id:: ". $error['reference'];
            } else {
                $message .= "<br>($i) " . $error[self::TARGETID];
            }

            $i++;
        }

        return $message .= "<br/><br/>The details of these Errors are attached.";
    }

    /**
     * Update the notification list as sen
     * @param $emailSent
     *
     * @return bool|null
     */
    public function updateNotification($emailSent)
    {
        try {
            if (empty($this->notificationList) || $emailSent) {
                return null;
            }

            if (isset($this->notificationList["erp"]) && !empty($this->notificationList["erp"])) {
                $condition = "`msg_id` IN ('".implode("','", $this->notificationList["erp"])."') AND `origin` = 'erp'";
                $this->errorData->create()->getCollection()
                ->setTableRecords(
                    $condition,
                    [self::NOTIFICATIONSENT => 1]
                );
            }

            if (isset($this->notificationList[self::MAGENTO]) && !empty($this->notificationList["erp"])) {
                $condition = "`msg_id` IN ('".implode("','", $this->notificationList[self::MAGENTO])."')";
                $condition .= " AND `origin` = 'magento'";
                $this->errorData->create()->getCollection()
                ->setTableRecords(
                    $condition,
                    [self::NOTIFICATIONSENT => 1]
                );
            }

        } catch (LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), Generic::I95REPORT, Generic::CRITICAL);
        }
        return true;
    }

    /**
     * Delete report file after successful mail sent.
     *
     * @param string $filePath
     */
    public function deleteReportFile($filePath)
    {
        try {
            $path = $this->directory_list->getPath('var') . DS . "log" . DS . $filePath;
            $this->driverInterface->deleteFile($path);
        } catch (FileSystemException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), Generic::I95REPORT, Generic::CRITICAL);
        }
    }
}
