<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_AgeVerification
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AgeVerification\Api\Data;

/**
 * Interface GeneralConfigInterface
 * @package Mageplaza\AgeVerification\Api\Data
 */
interface GeneralConfigInterface
{
    const VERIFY_AGE             = 'verify_age';
    const ENABLED_TERM_CONDITION = 'enabled_term_condition';
    const LINK_TERM              = 'link_term';
    const ANCHOR_TEXT            = 'anchor_text';
    const ANCHOR_URL             = 'anchor_url';
    const COOKIE_TIME            = 'cookie_time';
    const CUSTOMER_GROUPS        = 'customer_groups';
    const AUTO_VERIFY            = 'auto_verify';
    const REDIRECT               = 'redirect';

    /**
     * @return float
     */
    public function getVerificationAge(): float;

    /**
     * @return bool
     */
    public function getIsEnableTermCondition(): bool;

    /**
     * @return string
     */
    public function getLinkTitle(): string;

    /**
     * @return string
     */
    public function getAnchorText(): string;

    /**
     * @return string
     */
    public function getAnchorUrl(): string;

    /**
     * @return float
     */
    public function getCookieLifetime(): float;

    /**
     * @return int[]
     */
    public function getCustomerGroups(): array;

    /**
     * @return bool
     */
    public function getIsAutoVerify(): bool;

    /**
     * @return string
     */
    public function getRedirectUrl(): string;
}
