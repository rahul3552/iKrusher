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
namespace Aheadworks\OneStepCheckout\Model;

use Aheadworks\OneStepCheckout\Api\NewsletterSubscriberManagementInterface;
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriberFactory;

/**
 * Class NewsletterSubscriberManagement
 * @package Aheadworks\OneStepCheckout\Model
 */
class NewsletterSubscriberManagement implements NewsletterSubscriberManagementInterface
{
    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @param SubscriberFactory $subscriberFactory
     */
    public function __construct(SubscriberFactory $subscriberFactory)
    {
        $this->subscriberFactory = $subscriberFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function isSubscribedByEmail($email)
    {
        /** @var Subscriber $subscriber */
        $subscriber = $this->subscriberFactory->create();
        return $subscriber
            ->loadByEmail($email)
            ->isSubscribed();
    }
}
