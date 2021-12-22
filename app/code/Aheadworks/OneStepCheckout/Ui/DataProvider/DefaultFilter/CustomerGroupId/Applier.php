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
namespace Aheadworks\OneStepCheckout\Ui\DataProvider\DefaultFilter\CustomerGroupId;

use Aheadworks\OneStepCheckout\Model\ResourceModel\Report\FilterableInterface;
use Aheadworks\OneStepCheckout\Ui\DataProvider\DefaultFilter\CustomerGroupId as Filter;
use Aheadworks\OneStepCheckout\Ui\DataProvider\DefaultFilterApplierInterface;
use Magento\Customer\Model\GroupManagement;

/**
 * Class Applier
 * @package Aheadworks\OneStepCheckout\Ui\DataProvider\DefaultFilter\CustomerGroupId
 */
class Applier implements DefaultFilterApplierInterface
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @param Filter $filter
     */
    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(FilterableInterface $collection)
    {
        $customerGroupId = $this->filter->getCustomerGroupId();
        if ($customerGroupId != GroupManagement::CUST_GROUP_ALL) {
            $collection->addCustomerGroupIdFilter($customerGroupId);
        }
    }
}
