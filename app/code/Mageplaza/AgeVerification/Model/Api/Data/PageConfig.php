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

namespace Mageplaza\AgeVerification\Model\Api\Data;

use Magento\Framework\DataObject;
use Mageplaza\AgeVerification\Api\Data\PageConfigInterface;

/**
 * Class PageConfig
 * @package Mageplaza\AgeVerification\Model\Api\Data
 */
class PageConfig extends DataObject implements PageConfigInterface
{
    /**
     * @inheritDoc
     */
    public function getApplyFor()
    {
        return $this->getData(self::APPLY_FOR);
    }

    /**
     * @inheritDoc
     */
    public function getApplyForCms()
    {
        return $this->getData(self::APPLY_FOR_CMS);
    }

    /**
     * @inheritDoc
     */
    public function getApplyForCategories()
    {
        return $this->getData(self::APPLY_FOR_CATEGORY);
    }

    /**
     * @inheritDoc
     */
    public function getIncludePages(): array
    {
        return (array)$this->getData(self::INCLUDE_PAGES);
    }

    /**
     * @inheritDoc
     */
    public function getExcludePages(): array
    {
        return (array)$this->getData(self::EXCLUDE_PAGES);
    }

    /**
     * @inheritDoc
     */
    public function getAppliedProducts()
    {
        return $this->getData(self::PRODUCT_IDS);
    }
}
