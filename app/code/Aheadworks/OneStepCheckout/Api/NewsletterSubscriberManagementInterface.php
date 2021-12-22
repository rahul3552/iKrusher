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
namespace Aheadworks\OneStepCheckout\Api;

/**
 * Interface NewsletterSubscriberManagementInterface
 * @package Aheadworks\OneStepCheckout\Api
 * @api
 */
interface NewsletterSubscriberManagementInterface
{
    /**
     * Check if there is a newsletter subscription by given email
     *
     * @param string $email
     * @return bool
     */
    public function isSubscribedByEmail($email);
}
