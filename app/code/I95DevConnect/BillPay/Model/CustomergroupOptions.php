<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Model;

/**
 * Model class for Customer Group as Options
 */
class CustomergroupOptions extends \Magento\Framework\DataObject implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * Customer Group
     *
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    protected $customerGroup;

    /**
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup
    ) {
        $this->customerGroup = $customerGroup;
    }

    public function toOptionArray()
    {
        return $this->customerGroup->toOptionArray();
    }
}
