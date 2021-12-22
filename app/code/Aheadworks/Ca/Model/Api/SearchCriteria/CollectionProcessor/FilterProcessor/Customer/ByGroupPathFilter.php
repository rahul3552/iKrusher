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
namespace Aheadworks\Ca\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor\Customer;

use Aheadworks\Ca\Api\Data\GroupInterface;
use Aheadworks\Ca\Model\ResourceModel\Group;
use Magento\Customer\Model\ResourceModel\Customer\Collection;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class ByGroupPathFilter
 * @package Aheadworks\Ca\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor\Customer
 */
class ByGroupPathFilter implements CustomFilterInterface
{
    /**
     * Apply group path filter to collection
     *
     * @param Filter $filter
     * @param AbstractDb $collection
     * @return bool
     */
    public function apply(Filter $filter, AbstractDb $collection)
    {
        $fieldToFilter = GroupInterface::PATH;
        /** @var Collection $collection */
        $collection->getSelect()
            ->joinLeft(
                ['awcag' => $collection->getTable(Group::MAIN_TABLE_NAME)],
                'awcag.id = extension_attribute_aw_ca_company_user.company_group_id',
                []
            );
        $collection->addFilterToMap($fieldToFilter, 'awcag.' . $fieldToFilter);
        $collection->addFilter($fieldToFilter, ['like' => $filter->getValue() . '%'], 'public');

        return true;
    }
}
