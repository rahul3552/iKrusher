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
 * @package    CreditLimit
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor\Customer;

use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Magento\Customer\Model\ResourceModel\Customer\Collection;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DB\Select;

/**
 * Class DefaultSummaryByGroupId
 *
 * @package Aheadworks\CreditLimit\Model\Api\SearchCriteria\CollectionProcessor\FilterProcessor\Customer
 */
class DefaultSummaryByGroupId implements CustomFilterInterface
{
    /**
     * Apply group ID filter to customer summary collection
     *
     * It retrieves customers with default credit limit value for specified group
     * Customers with custom credit limit value are ignored
     *
     * @param Filter $filter
     * @param AbstractDb $collection
     * @return bool
     */
    public function apply(Filter $filter, AbstractDb $collection)
    {
        $collectionSelect = $collection->getSelect();
        $collectionSelect->reset(Select::HAVING);
        $collectionSelect->having('group_id = ?', $filter->getValue());
        $collectionSelect->having(SummaryInterface::IS_CUSTOM_CREDIT_LIMIT . ' = ?', 0);

        return true;
    }
}
