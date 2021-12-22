<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_ErrorData
 */

namespace I95DevConnect\ErrorData\Helper;

/*
 * Generic Helper Class for Sending the Error Report to the Customer
 * @author Ranjith R
*/

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Generic Helper for error reporting
 */
class Generic
{
    const XML_PATH_ENABLED = 'i95devconnect_errors/reports_enabled_settings/report';
    const I95REPORT = "i95devErrorReport";
    const CRITICAL = "critical";
    const REPORT_TYPE = 'i95devconnect_errors/reports_enabled_settings/report_type';
    const REPORT_ENTITIES = 'i95devconnect_errors/reports_enabled_settings/report_entities';

    /**
     * @var \I95DevConnect\MessageQueue\Api\LoggerInterface
     */
    public $logger;

    /**
     * @var \I95DevConnect\MessageQueue\Api\I95DevErpMQRepositoryInterface
     */
    public $i95DevErpMQRepository;

    /**
     * @var \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterface
     */
    public $i95DevMagentoMQ;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param \I95DevConnect\MessageQueue\Api\I95DevErpMQRepositoryInterfaceFactory $i95DevErpMQRepository
     * @param \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQ
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \I95DevConnect\MessageQueue\Api\I95DevErpMQRepositoryInterfaceFactory $i95DevErpMQRepository,
        \I95DevConnect\MessageQueue\Api\I95DevMagentoMQRepositoryInterfaceFactory $i95DevMagentoMQ,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->logger = $logger;
        $this->i95DevErpMQRepository = $i95DevErpMQRepository;
        $this->i95DevMagentoMQ = $i95DevMagentoMQ;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check if module is enable
     * @return string
     * @author Ranjith R
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if module is enable
     * @return string
     * @author Ranjith R
     */
    public function getReportType()
    {
        return $this->scopeConfig->getValue(
            self::REPORT_TYPE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get the inbound message queue record
     * @param int $msgId
     * @return null|I95DevConnect\MessageQueue\Api\I95DevErpMQ
     * @author Ranjith R
     */
    public function getIMQData($msgId)
    {
        try {
            return $this->i95DevErpMQRepository->create()->load($msgId);
        } catch (LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), self::I95REPORT, self::CRITICAL);
            return null;
        }
    }

    /**
     * Get the outbound message queue record
     * @param int $msgId
     * @return null|I95DevConnect\MessageQueue\Api\I95DevMagentoMQ
     * @author Ranjith R
     */
    public function getOMQData($msgId)
    {
        try {
            return $this->i95DevMagentoMQ->create()->load($msgId);
        } catch (LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), self::I95REPORT, self::CRITICAL);
            return null;
        }
    }

    /**
     * Get the contact details to whom the error notification has to be sent
     * @return array
     * @author Ranjith R
     */
    public function getContactDetails()
    {
        try {
            $fromEmail = $this->scopeConfig->getValue(
                'trans_email/ident_general/email',
                ScopeInterface::SCOPE_STORE
            );
            $from = $this->scopeConfig->getValue(
                'trans_email/ident_general/name',
                ScopeInterface::SCOPE_STORE
            );
            $recieverEmail = $this->scopeConfig->getValue(
                'i95dev_messagequeue/I95DevConnect_generalcontact/email_sent'
            );
            $reciever = $this->scopeConfig->getValue('i95dev_messagequeue/I95DevConnect_generalcontact/username');

            $cc = $this->scopeConfig->getValue(
                'i95dev_messagequeue/I95DevConnect_generalcontact/email_cc',
                ScopeInterface::SCOPE_STORE
            );
            $ccList = [];
            if ($cc) {
                $ccList = explode(",", $cc);
            }

            return [
                'from_email' => $fromEmail,
                'from_name' => $from,
                'reciever_email' => $recieverEmail,
                'reciever_name' => $reciever,
                'cc' => $ccList
            ];
        } catch (LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), self::I95REPORT, self::CRITICAL);
            return [];
        }
    }

    /**
     * Return all enabled entities for which error report will be sent.
     *
     * @return array
     * @author Debashis S. Gopal
     */
    public function getEnabledEntities()
    {
        $entities =  $this->scopeConfig->getValue(
            self::REPORT_ENTITIES,
            ScopeInterface::SCOPE_STORE
        );
        return explode(",", $entities);
    }
}
