<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Model\Newsletter;

use Aheadworks\OneStepCheckout\Model\Config;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Store\Model\ScopeInterface;

/**
 * Class ConfigProvider
 * @package Aheadworks\OneStepCheckout\Model\Newsletter
 */
class ConfigProvider
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @param Config $config
     * @param ScopeConfigInterface $scopeConfig
     * @param CustomerSession $customerSession
     * @param SubscriberFactory $subscriberFactory
     */
    public function __construct(
        Config $config,
        ScopeConfigInterface $scopeConfig,
        CustomerSession $customerSession,
        SubscriberFactory $subscriberFactory
    ) {
        $this->config = $config;
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
        $this->subscriberFactory = $subscriberFactory;
    }

    /**
     * Get newsletter subscribe option config
     *
     * @return array
     */
    public function getConfig()
    {
        $config = [
            'isEnabled' => $this->config->isNewsletterSubscribeOptionEnabled(),
            'isChecked' => $this->config->isNewsletterSubscribeOptionCheckedByDefault(),
            'isSubscribed' => false,
            'isGuestSubscriptionsAllowed' => $this->scopeConfig->isSetFlag(
                Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG,
                ScopeInterface::SCOPE_STORE
            )
        ];
        if ($this->customerSession->isLoggedIn()) {
            /** @var Subscriber $subscriber */
            $subscriber = $this->subscriberFactory->create();
            $config['isSubscribed'] = $subscriber
                ->loadByCustomerId($this->customerSession->getCustomerId())
                ->isSubscribed();
        }
        return $config;
    }
}
