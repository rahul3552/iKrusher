<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_ErrorData
 */

namespace I95DevConnect\ErrorData\Controller\Index;

use I95DevConnect\ErrorData\Model\ScheduledReport;
use I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use \I95DevConnect\ErrorData\Helper\Generic;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Module\Manager;
use Magento\Store\Model\ScopeInterface;
use \Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;

/*
 * Controller Class for Sending the Error Report to the Customer which is the trigger point
 * @author Ranjith R
*/

/**
 * report controller for error data class
 */
class Report extends Action
{
    /**
     * @var ScheduledReport
     */
    public $scheduledReport;

    /**
     * @var Manager
     */
    public $moduleManager = null;

    /**
     * @var LoggerInterfaceFactory
     */
    public $logger;

    /**
     * scopeConfig for system Configuration
     *
     * @var string
     */
    public $scopeConfig;

    /**
     * @var Generic
     */
    public $genericHelper;
    /**
     * @var StoreManagerInterface
     */
    public $storeManager;

    /**
     *
     * @param Context $context
     * @param ScheduledReport $scheduledReport
     * @param ScopeConfigInterface $scopeConfig
     * @param Manager $moduleManager
     * @param Generic $genericHelper
     * @param LoggerInterfaceFactory $logger
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        ScheduledReport $scheduledReport,
        ScopeConfigInterface $scopeConfig,
        Manager $moduleManager,
        Generic $genericHelper,
        LoggerInterfaceFactory $logger,
        StoreManagerInterface $storeManager
    ) {
        $this->scheduledReport = $scheduledReport;
        $this->scopeConfig = $scopeConfig;
        $this->moduleManager = $moduleManager;
        $this->logger = $logger;
        $this->genericHelper = $genericHelper;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * This method willbe called by cron, which will send scheduled error report.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        try {
            $isReportEnabled = $this->scopeConfig->getValue(
                'i95devconnect_errors/reports_enabled_settings/report',
                ScopeInterface::SCOPE_WEBSITE,
                $this->storeManager->getDefaultStoreView()->getWebsiteId()
            );
            $isModEnable = $this->moduleManager->isEnabled('I95DevConnect_ErrorData');
            $reportType = $this->genericHelper->getReportType();
            if ($isReportEnabled && $isModEnable && $reportType == "Schedule") {
                $this->scheduledReport->sendReport();
            }
        } catch (LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), Generic::I95REPORT, 'critical');
        }
    }
}
