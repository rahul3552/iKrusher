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
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Quote\Api\Data\PaymentInterface;

/**
 * Class PaymentDataExtensionProcessor
 * @package Aheadworks\OneStepCheckout\Model\Newsletter
 */
class PaymentDataExtensionProcessor
{
    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var bool
     */
    private $isSubscribedFlag = false;

    /**
     * @param SubscriberFactory $subscriberFactory
     * @param Config $config
     * @param CustomerSession $customerSession
     */
    public function __construct(
        SubscriberFactory $subscriberFactory,
        Config $config,
        CustomerSession $customerSession
    ) {
        $this->subscriberFactory = $subscriberFactory;
        $this->config = $config;
        $this->customerSession = $customerSession;
    }

    /**
     * Process subscriber extension attributes of payment data
     *
     * @param PaymentInterface $paymentData
     * @return void
     * @throws \Exception
     */
    public function process(PaymentInterface $paymentData)
    {
        if ($this->config->isNewsletterSubscribeOptionEnabled() && !$this->isSubscribedFlag) {
            $isSubscribeFlag = $paymentData->getExtensionAttributes() === null
                ? false
                : $paymentData->getExtensionAttributes()->getIsSubscribeForNewsletter();

            if ($isSubscribeFlag) {
                /** @var Subscriber $subscriber */
                $subscriber = $this->subscriberFactory->create();
                if ($this->customerSession->isLoggedIn()) {
                    $customerId = $this->customerSession->getCustomerId();
                    if (!$this->isSubscribedByCustomerId($customerId)) {
                        $subscriber->subscribeCustomerById($customerId);
                        $this->isSubscribedFlag = true;
                    }
                } else {
                    $email = $paymentData->getExtensionAttributes() === null
                        ? false
                        : $paymentData->getExtensionAttributes()->getSubscriberEmail();
                    if ($email && !$this->isSubscribedByEmail($email)) {
                        $subscriber->subscribe($email);
                        $this->isSubscribedFlag = true;
                    }
                }
            }
        }
    }

    /**
     * Check if subscribed by customer ID
     *
     * @param int $customerId
     * @return bool
     */
    private function isSubscribedByCustomerId($customerId)
    {
        /** @var Subscriber $subscriber */
        $subscriber = $this->subscriberFactory->create();
        return $subscriber
            ->loadByCustomerId($customerId)
            ->isSubscribed();
    }

    /**
     * Check if subscribed by email
     *
     * @param string $email
     * @return bool
     */
    private function isSubscribedByEmail($email)
    {
        /** @var Subscriber $subscriber */
        $subscriber = $this->subscriberFactory->create();
        return $subscriber
            ->loadByEmail($email)
            ->isSubscribed();
    }
}
