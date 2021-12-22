<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 * @author Divya Koona.Logger interface added to constructor
 */

namespace I95DevConnect\MessageQueue\Helper;

use \Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;

/**
 * Class for sending order mismatch mail
 */
class OrderMismatchEmail extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * scopeConfig for system Congiguration
     *
     * @var string
     */
    public $scopeConfig;

    /**
     * @var \Magento\AdminNotification\Model\InboxFactory
     */
    public $inboxFactory;

    /**
     *
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    public $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    public $transportBuilder;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * Logger Interface
     *
     * @var \I95DevConnect\MessageQueue\Api\LoggerInterface
     */
    public $logger;

    /**
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\AdminNotification\Model\InboxFactory $inboxFactory
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterface $logger
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\AdminNotification\Model\InboxFactory $inboxFactory,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \I95DevConnect\MessageQueue\Api\LoggerInterface $logger,
        Context $context
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->inboxFactory = $inboxFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * compares TargetValue with local store value and sends notification and email
     *
     * @param string $storeValue
     * @param string $targetValue
     *
     * @param $orderId
     * @param $createdDate
     * @param $flag
     *
     * @throws \Exception
     * @author Divya Koona. Generic exception changed to Localized exception and
     * logging mechanism modified in catch blocks.
     */
    public function compareTargetValue($storeValue, $targetValue, $orderId, $createdDate, $flag)
    {
        try {
            if ((string) $storeValue != $targetValue && !$flag) {
                $isEnabled = $this->getscopeConfig(
                    'i95dev_messagequeue/I95DevConnect_notifications/order_totalmismatch',
                    ScopeInterface::SCOPE_WEBSITE,
                    $this->storeManager->getDefaultStoreView()->getWebsiteId()
                );
                if ($isEnabled) {
                    $this->sendNotification($orderId);
                    $i95devGeneralContact = 'i95dev_messagequeue/I95DevConnect_generalcontact/username';
                    $userName = $this->getscopeConfig(
                        $i95devGeneralContact,
                        ScopeInterface::SCOPE_WEBSITE,
                        $this->storeManager->getDefaultStoreView()->getWebsiteId()
                    );
                    $emailTemplateVariables['user_name'] = $userName;
                    $emailTemplateVariables['order_number'] = $orderId;
                    $emailTemplateVariables['order_total'] = $storeValue;
                    $emailTemplateVariables['target_order_total'] = $targetValue;
                    $emailTemplateVariables['order_created_at'] = date("Y-M-d", strtotime($createdDate));
                    $this->sendMail($emailTemplateVariables);
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $misMatchMsg = "Order Total Mismatch notification mail not sent";
            $this->logger->createLog(
                __METHOD__,
                $misMatchMsg,
                \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                \I95DevConnect\MessageQueue\Api\LoggerInterface::CRITICAL
            );
        }
    }

    /**
     * send Notification in admin dashboard
     *
     * @param int $id
     *
     * @throws \Exception
     * @author Divya Koona. Generic exception changed to Localized exception and
     * logging mechanism modified in catch blocks.
     */
    public function sendNotification($id)
    {
        try {
            $notification = $this->inboxFactory->create();
            $notification->setData('severity', 3);
            $notification->setData('date_added', gmdate('Y-m-d H:i:s'));
            $notification->setData('title', 'Order Total Mismatch');
            $notification->setData('description', "The total amount is mismatched in Target for the order id  $id");
            $notification->save();
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(
                __METHOD__,
                $ex->getMessage(),
                \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                \I95DevConnect\MessageQueue\Api\LoggerInterface::CRITICAL
            );
        }
    }

    /**
     * Will send Mail based on template
     *
     * @param sting $emailTemplateVariables
     *
     * @author Divya Koona. Generic exception changed to Localized exception and
     * logging mechanism modified in catch blocks.
     */
    public function sendMail($emailTemplateVariables)
    {
        try {
            $i95devSetting_component = 'i95dev_messagequeue/I95DevConnect_settings/component';
            $component = $this->getscopeConfig(
                $i95devSetting_component,
                ScopeInterface::SCOPE_WEBSITE,
                $this->storeManager->getDefaultStoreView()->getWebsiteId()
            );
            $this->inlineTranslation->suspend();
            $emailTemplateVariables['component'] = $component;
            $i95DevConnect_generalcontact = 'i95dev_messagequeue/I95DevConnect_generalcontact/email_sent';
            $generalEmail = $this->getscopeConfig(
                $i95DevConnect_generalcontact,
                ScopeInterface::SCOPE_WEBSITE,
                $this->storeManager->getDefaultStoreView()->getWebsiteId()
            );
            $emails = explode(',', $generalEmail);
            // @updatedBy Subhan. If there is only one email then pass it as string
            if (empty($emails[1])) {
                $emails = $generalEmail;
            }
            $senderEmail = $this->getscopeConfig(
                $i95DevConnect_generalcontact,
                ScopeInterface::SCOPE_WEBSITE,
                $this->storeManager->getDefaultStoreView()->getWebsiteId()
            );
            $generalcontact_username = 'i95dev_messagequeue/I95DevConnect_generalcontact/username';
            $sendername = $this->getscopeConfig(
                $generalcontact_username,
                ScopeInterface::SCOPE_WEBSITE,
                $this->storeManager->getDefaultStoreView()->getWebsiteId()
            );
            $senderInfo = ['name' => $sendername, 'email' => $senderEmail];
            $storeId = $this->storeManager->getStore()->getId();
            $this->transportBuilder->setTemplateIdentifier('i95devconnect_base_order_total_mismatch')
                    ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_ADMINHTML, 'store' => $storeId])
                    ->setTemplateVars($emailTemplateVariables)
                    ->setFrom($senderInfo)
                    ->addTo($emails);

            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(
                __METHOD__,
                $ex->getMessage(),
                \I95DevConnect\MessageQueue\Api\LoggerInterface::I95EXC,
                \I95DevConnect\MessageQueue\Api\LoggerInterface::CRITICAL
            );
        }
    }

    /**
     * get Scope Config Object
     * @param string $value
     * @param string $scope
     * @param $id
     * @return obj
     */
    public function getscopeConfig($value, $scope, $id)
    {
        return $this->scopeConfig->getValue($value, $scope, $id);
    }
}
