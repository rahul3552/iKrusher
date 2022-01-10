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
 * Interface PurchaseConfigInterface
 * @package Mageplaza\AgeVerification\Api\Data
 */
interface PurchaseConfigInterface
{
    const ENABLED     = 'enabled';
    const PRODUCT_IDS = 'product_ids';
    const NOTICE_TYPE = 'notice_type';
    const IMAGE       = 'image';
    const MESSAGE     = 'message';

    /**
     * @return boolean
     */
    public function getIsEnable(): bool;

    /**
     * @return int[]|null
     */
    public function getAppliedProducts();

    /**
     * @return string
     */
    public function getAgeNotice(): string;

    /**
     * @return string
     */
    public function getImage(): string;

    /**
     * @return string
     */
    public function getMessage(): string;
}
