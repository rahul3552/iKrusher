<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_ErrorData
 */

namespace I95DevConnect\ErrorData\Model;

use \I95DevConnect\ErrorData\Helper\Generic;
use I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use \I95DevConnect\ErrorData\Model\ScheduledReport;

/**
 * Class InstantReport For sending instant error notification.
 */
class InstantReport
{
    const NOTIFICATIONSENT = 'notification_sent';
    const MSGID = "msg_id";
    const MESSAGE = "message";
    const MAGENTO = "magento";

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
        'Target Id',
        'Created Date',
        'Updated Date',
        'ErrorMessage'
    ];
    public $notificationList = [];
    public $entity;
    public $targetId;
    public $origin;
    public $sourceId;
    public $reference = '';

    /**
     *
     * @param DirectoryList $directory_list
     * @param LoggerInterfaceFactory $logger
     * @param ErrorDataFactory $errorData
     * @param ErrorMessageDataFactory $errorMsgData
     * @param Generic $genericHelper
     * @param Email $email
     * @param \I95DevConnect\ErrorData\Model\ScheduledReport $scheduledReport
     */
    public function __construct(
        DirectoryList $directory_list,
        LoggerInterfaceFactory $logger,
        ErrorDataFactory $errorData,
        ErrorMessageDataFactory $errorMsgData,
        Generic $genericHelper,
        Email $email,
        ScheduledReport $scheduledReport
    ) {
        $this->directory_list = $directory_list;
        $this->logger = $logger;
        $this->errorData = $errorData;
        $this->errorMsgData = $errorMsgData;
        $this->genericHelper = $genericHelper;
        $this->email = $email;
        $this->scheduledReport = $scheduledReport;
    }

    /**
     * Get the list of errors and Send report to the customer
     *
     * @param int $msgId
     * @param $origin
     *
     * @author Ranjith R
     */
    public function sendReport($msgId, $origin)
    {
        try {
            if ($this->genericHelper->isEnabled()) {
                $this->origin = $origin;
                $ibMqErrorList = $this->getMQErrorRecords($msgId);
                $emailSent = $this->sendEmail($ibMqErrorList);
                $this->updateNotification($emailSent);
            }
        } catch (LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), Generic::I95REPORT, Generic::CRITICAL);
        }
    }

    /**
     * Get Message Queue Records which has errors
     * @param int $msgId
     * @return array $mqList
     * @author Ranjith R
     */
    public function getMQErrorRecords($msgId)
    {
        try {
            $mqList = $errorMsgList = [];
            $errorLogRecData = $this->errorData->create()->getCollection()
                ->addFieldToFilter(self::NOTIFICATIONSENT, [['neq' => 1], ['null' => true]])
                ->addFieldToFilter('origin', $this->origin)
                ->addFieldToFilter(self::MSGID, $msgId)
                ->getLastItem()->getData();
            if (!empty($errorLogRecData)) {
                $errorMsgList = $this->errorMsgData->create()->getCollection()
                    ->addFieldToFilter("notification_id", $errorLogRecData['id'])
                    ->getData();
            }

            if (empty($errorMsgList)) {
                return $mqList;
            }
            $this->notificationList[$this->origin][] = $errorLogRecData[self::MSGID];
            return array_merge($mqList, $this->getMessageList($errorLogRecData, $errorMsgList));

        } catch (LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), Generic::I95REPORT, Generic::CRITICAL);
        }

        return $mqList;
    }

    /**
     * Get the list of error messages
     * @param type $errorLogRecData
     * @param type $errorMsgList
     * @return array
     * @author Ranjith R
     */
    public function getMessageList($errorLogRecData, $errorMsgList)
    {
        $msgList = [];
        $msgId = $errorLogRecData[self::MSGID];
        try {
            if ($this->origin == "erp") {
                $mqData = $this->genericHelper->getIMQData($msgId);
            } else {
                $mqData = $this->genericHelper->getOMQData($msgId);
            }

            if ($mqData) {
                $this->entity = $mqData->getEntityCode();
                $this->targetId = $mqData->getTargetId();
                $this->sourceId = $mqData->getMagentoId();
                $this->reference = $mqData->getRefName();
                $i = 0;
                foreach ($errorMsgList as $errorMsgData) {
                    $i++;
                    $msgList[] = [
                        'msgId' => $msgId,
                        self::MESSAGE => $errorMsgData[self::MESSAGE]
                    ];
                }
            }
        } catch (LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), Generic::I95REPORT, Generic::CRITICAL);
        }

        return $msgList;
    }

    /**
     * Send error report as an email to customer
     * @param $mqErrorList
     * @return boolean
     * @author Ranjith R
     */
    public function sendEmail($mqErrorList)
    {
        try {
            if (empty($mqErrorList)) {
                return null;
            }

            $mailDetails = $this->genericHelper->getContactDetails();
            $template = 'i95devconnect_error_report';

            $subject = $this->getSubject();
            $message = $this->getMessage($mqErrorList);
            return $this->email->sendEmail(null, $mailDetails, $template, $subject, $message);
        } catch (LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), Generic::I95REPORT, Generic::CRITICAL);
            return false;
        }
    }

    /**
     * Get the subject for the email
     * @return string $message
     * @author Ranjith R
     */
    public function getSubject()
    {
        if ($this->origin == "erp") {
            $message = "i95Dev ". ucfirst($this->entity) ." Sync failure | ";
            $message .= ucfirst($this->entity)." ID: ".$this->targetId;
        } elseif ($this->origin == self::MAGENTO) {
            $message = "i95Dev Notification System | ".$this->entity;
            $message .= " creation from Magento to ERP failed with Magento ".$this->entity." ID ".$this->sourceId;
        } else {
            $message = "i95Dev Notification System | ".$this->entity." creation failed";
        }

        return $message;
    }

    /**
     * Get the message body for the email
     * @param $mqErrorList
     * @return string $message
     * @author Ranjith R
     */
    public function getMessage($mqErrorList)
    {
        if (empty($mqErrorList)) {
            return "";
        }

        if ($this->origin == "erp") {
            $message = ucfirst($this->entity) . " creation from ERP to Magento with ERP " .
                ucfirst($this->entity) . " ID <b>" . $this->targetId;
            if ($this->entity === 'address') {
                $message .= ' of Customer ID ' . $this->reference;
            }
            $message .= "</b> has failed due to the below reasons";
        } elseif ($this->origin == self::MAGENTO) {
            $message = $this->entity." creation from Magento to ERP with-

             Magento ". ucfirst($this->entity)." ID <b>";
            $message .= $this->sourceId."</b> has failed due to the below reasons";
        } else {
            $message = $this->entity." creation has failed due to the below reasons";
        }
        $message .= $this->getInstructions();

        $message .= "<br/><table><col width='20%'><col width='80%'>";
        $message .= "<tr style='border: 1px solid black; padding: 0 10px; width: 100px'>
        <th style='border: 1px solid black;'>Message ID</th>";
        $message .= "<th style='border: 1px solid black; padding: 0 10px; width: 100px'>ERP ID</th>";
        $message .= "<th style='border: 1px solid black; padding: 0 10px; width: 100px'>Reference</th>";
        $message .= "<th style='border: 1px solid black; padding: 0 10px; width: 100px'>Error Message</th></tr>";
        foreach ($mqErrorList as $mqError) {
            $msgId = $mqError['msgId'];
            $msg = $mqError[self::MESSAGE];
            $message .= "<tr style='border: 1px solid black; white-space: nowrap;'>";
            $message .= "<td style='border: 1px solid black; padding: 0 10px; width: 100px'>$msgId</td>";
            $message .= "<td style='border: 1px solid black; padding: 0 10px; width: 100px'>$this->targetId</td>";
            $message .= "<td style='border: 1px solid black; padding: 0 10px; width: 100px'>$this->reference</td>";
            $message .= "<td style='border: 1px solid black; padding: 0 10px; width: 100px'>$msg</td></tr>";
        }
        return $message . "</table><br>";
    }

    /**
     * Prepare Instruction string for Email body.
     *
     * @return string
     */
    public function getInstructions()
    {
        return  "<br/><br/><div style='background-color: #E0E0E0'><b><u>Please follow below Instructions.</u>
                 <ul><li>Log in to the Magento Admin Dashboard. And Navigate to I95Dev ->Inbound MessageQueue</li>
                  <li>Copy the ERP ID from the email and seach on the grid by pasting \"ERP id\" input box.</li>
                  <li>Please click on the error link to view the error message, 
                  if it is a data issue please contact NAV team.</li>
                  </ul></b></div>";
    }

    /**
     * Update the notification list as sent
     *
     * @param boolean $emailSent
     */
    public function updateNotification($emailSent)
    {
        $this->scheduledReport->updateNotification($emailSent);
    }
}
