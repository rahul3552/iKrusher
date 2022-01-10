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
 * Interface ConfigsInterface
 * @package Mageplaza\AgeVerification\Api\Data
 */
interface ConfigsInterface
{
    const GENERAL         = 'general';
    const PAGE_VERIFY     = 'page_verify';
    const PURCHASE_VERIFY = 'purchase_verify';
    const DESIGN          = 'design';

    /**
     * @return \Mageplaza\AgeVerification\Api\Data\GeneralConfigInterface|null
     */
    public function getGeneralConfig();

    /**
     * @return \Mageplaza\AgeVerification\Api\Data\PageConfigInterface|null
     */
    public function getPageConfig();

    /**
     * @return \Mageplaza\AgeVerification\Api\Data\PurchaseConfigInterface|null
     */
    public function getPurchaseConfig();

    /**
     * @return \Mageplaza\AgeVerification\Api\Data\DesignConfigInterface|null
     */
    public function getDesignConfig();
}
