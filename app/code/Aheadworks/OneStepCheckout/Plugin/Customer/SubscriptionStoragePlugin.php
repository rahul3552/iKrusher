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
namespace Aheadworks\OneStepCheckout\Plugin\Customer;

use Magento\Customer\Model\Delegation\Storage;
use Magento\Customer\Model\Session;
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriberFactory;

/**
 * Class SubscriptionStoragePlugin
 * @package Aheadworks\OneStepCheckout\Plugin\Customer
 */
class SubscriptionStoragePlugin
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @param Session $session
     * @param SubscriberFactory $subscriberFactory
     */
    public function __construct(
        Session $session,
        SubscriberFactory $subscriberFactory
    ) {
        $this->session = $session;
        $this->subscriberFactory = $subscriberFactory;
    }

    /**
     * Add subscription result to customer form data
     *
     * @param Storage $subject
     */
    public function afterStoreNewOperation(Storage $subject)
    {
        $customerData = $this->session->getCustomerFormData();
        if (isset($customerData['email']) && $this->isSubscribedByEmail($customerData['email'])) {
            $customerData['is_subscribed'] = true;
            $this->session->setCustomerFormData($customerData);
        }
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
        $subscriber = $this->subscriberFactory->create()->loadByEmail($email);
        return $subscriber
            && ($subscriber->getStatus() == Subscriber::STATUS_SUBSCRIBED
                || $subscriber->getStatus() == Subscriber::STATUS_NOT_ACTIVE
            );
    }
}
