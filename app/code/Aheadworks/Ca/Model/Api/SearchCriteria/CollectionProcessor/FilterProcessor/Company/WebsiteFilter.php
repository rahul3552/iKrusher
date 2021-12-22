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
 * @package    Ca
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor\Company;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class WebsiteFilter
 * @package Aheadworks\Ca\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor\Company
 */
class WebsiteFilter implements CustomFilterInterface
{
    /**
     * Apply website filter to collection
     *
     * @param Filter $filter
     * @param AbstractDb $collection
     * @return bool
     */
    public function apply(Filter $filter, AbstractDb $collection)
    {
        $fieldToFilter = 'website_id';
        $collection->addWebsiteFilter();
        $collection->addFilterToMap($fieldToFilter, 'customer.' . $fieldToFilter);
        $collection->addFilter($fieldToFilter, $filter->getValue(), 'public');

        return true;
    }
}
