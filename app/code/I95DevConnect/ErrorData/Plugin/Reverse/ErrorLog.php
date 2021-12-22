<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_ErrorData
 */

namespace I95DevConnect\ErrorData\Plugin\Reverse;

use I95DevConnect\ErrorData\Model\ErrorDataFactory;
use I95DevConnect\ErrorData\Model\ErrorMessageDataFactory;
use I95DevConnect\ErrorData\Model\InstantReport;
use I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory;
use I95DevConnect\MessageQueue\Helper\Data;
use \I95DevConnect\MessageQueue\Model\AbstractDataPersistence;
use \I95DevConnect\ErrorData\Helper\Generic;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Plugin class responsible for saving the error information of ERP to Magento sync flow
 * @author Ranjith R
 */
class ErrorLog
{
    const ERP = "erp";

    public $errorData;
    public $date;
    public $errorMsgData;
    public $genericHelper;
    public $logger;

    /**
     * @var InstantReport
     */
    public $instantReport;

    /**
     *
     * @param ErrorDataFactory $errorData
     * @param ErrorMessageDataFactory $errorMsgData
     * @param LoggerInterfaceFactory $logger
     * @param DateTime $date
     * @param Generic $genericHelper
     * @param InstantReport $instantReport
     */
    public function __construct(
        ErrorDataFactory $errorData,
        ErrorMessageDataFactory $errorMsgData,
        LoggerInterfaceFactory $logger,
        DateTime $date,
        Generic $genericHelper,
        InstantReport $instantReport
    ) {
        $this->errorData = $errorData;
        $this->errorMsgData = $errorMsgData;
        $this->logger = $logger;
        $this->date = $date;
        $this->genericHelper = $genericHelper;
        $this->instantReport = $instantReport;
    }

    /**
     * before plugin method to validate the entity data required for order creation
     *
     * @param AbstractDataPersistence $subject
     * @param $status
     * @param $data
     * @param $message
     * @param $msgId
     *
     * @throws LocalizedException
     */
    public function beforeUpdateErpMQStatus(AbstractDataPersistence $subject, $status, $data, $message, $msgId)//NOSONAR
    {
        try {
            if ($status !== Data::ERROR || empty($message)) {
                return;
            }

            // phpcs:disable
            if (is_object($message) && get_class($message) == \Magento\Framework\Phrase::class) {
                $message = $message->getText();
            }
            // phpcs:enable

            $entityCode = $this->genericHelper->getIMQData($msgId)->getEntityCode();

            $errorLogRec = $this->errorData->create()
                ->getCollection()
                ->addFieldToFilter("msg_id", $msgId)
                ->addFieldToFilter("origin", self::ERP)
            ;
            $errorLogSize = $errorLogRec->getSize();
            if ($errorLogSize) {
                $this->deleteErrorMessageData($errorLogRec->getData(), $message, $status);
            }

            $this->updateErrorData($msgId, $message, $entityCode);
            if ($this->genericHelper->getReportType() == "Instant") {
                $this->sendReport($msgId, self::ERP, $entityCode);
            }
        } catch (\Exception $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), Generic::I95REPORT, Generic::CRITICAL);
            throw new LocalizedException(__($ex->getMessage()));
        }
    }

    /**
     * create the errors generated while validation under the error notification and message models
     * @param int $msgId
     * @param string $message
     * @param string $entityCode
     * @author Ranjith R
     */
    public function updateErrorData($msgId, $message, $entityCode)
    {
        try {
            if ($message) {
                $messageList = explode(";", $message);
                $errorDataModel = $this->errorData->create();
                $errorDataModel->setMsgId($msgId);
                $errorDataModel->setOrigin(self::ERP);
                $errorDataModel->setCreatedAt($this->date->gmtDate());
                $errorDataModel->setUpdatedAt($this->date->gmtDate());
                $errorDataModel->setEntityCode($entityCode);
                $errorDataModel->save();
                $errorId = $errorDataModel->getId();
                foreach ($messageList as $msg) {
                    $errorMsgDataModel = $this->errorMsgData->create();
                    $errorMsgDataModel->setNotificationId($errorId);
                    $errorMsgDataModel->setMessage($msg);
                    $errorMsgDataModel->save();
                }
            }
        } catch (\Exception $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), Generic::I95REPORT, Generic::CRITICAL);
        }
    }

    /**
     * delete the existing error message data of the respective message queue id
     *
     * @param array $errorLogRec
     *
     * @param $message
     * @param $status
     *
     * @return bool
     * @author Ranjith R
     */
    public function deleteErrorMessageData($errorLogRec, $message, $status)
    {
        try {
            if (!$message && $status == Data::PROCESSING) {
                return false;
            }
            foreach ($errorLogRec as $errorLogRecData) {
                $errorMsgList = $this->errorMsgData->create()
                    ->getCollection()
                    ->addFieldToFilter("notification_id", $errorLogRecData['id']);
                $errorSize = $errorMsgList->getSize();
                if ($errorSize) {
                    foreach ($errorMsgList as $errorMsgData) {
                        $errorMsgLog = $this->errorMsgData->create();
                        $errorMsgLog->load($errorMsgData->getId());
                        $errorMsgLog->delete();
                    }
                }

                $errorLog = $this->errorData->create();
                $errorLog->load($errorLogRecData['id']);
                $errorLog->delete();
            }
        } catch (\Exception $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), Generic::I95REPORT, Generic::CRITICAL);
        }
        return true;
    }

    /**
     * Send instant error report whenever an error occurs in record syncing.
     *
     * @param int $msgId
     * @param string $origin
     * @param string $entityCode
     */
    public function sendReport($msgId, $origin, $entityCode)
    {
        $inboundEntities = $this->genericHelper->getEnabledEntities();
        if (empty($inboundEntities) || !in_array($entityCode, $inboundEntities)) {
            return;
        }
        $this->instantReport->sendReport($msgId, $origin);
    }
}
