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
 * Interface PageConfigInterface
 * @package Mageplaza\AgeVerification\Api\Data
 */
interface PageConfigInterface
{
    const APPLY_FOR          = 'apply_for';
    const APPLY_FOR_CMS      = 'apply_for_cms';
    const APPLY_FOR_CATEGORY = 'apply_for_category';
    const INCLUDE_PAGES      = 'include_pages';
    const EXCLUDE_PAGES      = 'exclude_pages';
    const PRODUCT_IDS        = 'product_ids';

    /**
     * @return string[]|null
     */
    public function getApplyFor();

    /**
     * @return string[]|null
     */
    public function getApplyForCms();

    /**
     * @return int[]|null
     */
    public function getApplyForCategories();

    /**
     * @return string[]
     */
    public function getIncludePages(): array;

    /**
     * @return string[]
     */
    public function getExcludePages(): array;

    /**
     * @return int[]|null
     */
    public function getAppliedProducts();
}
