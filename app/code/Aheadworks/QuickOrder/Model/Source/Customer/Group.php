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
 * @package    QuickOrder
 * @version    1.0.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Model\Source\Customer;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Convert\DataObject;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Api\Data\GroupInterface;

/**
 * Class Group
 *
 * @package Aheadworks\QuickOrder\Model\Source\Customer
 */
class Group implements OptionSourceInterface
{
    /**
     * @var GroupManagementInterface
     */
    protected $groupManagement;

    /**
     * @var DataObject
     */
    private $objectConverter;

    /**
     * @param DataObject $objectConverter
     * @param GroupManagementInterface $groupManagement
     */
    public function __construct(
        DataObject $objectConverter,
        GroupManagementInterface $groupManagement
    ) {
        $this->objectConverter = $objectConverter;
        $this->groupManagement = $groupManagement;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function toOptionArray()
    {
        $groups[] = $this->getAllCustomersGroup();
        $groups[] = $this->groupManagement->getNotLoggedInGroup();
        $groups = array_merge($groups, $this->groupManagement->getLoggedInGroups());
        return $this->objectConverter->toOptionArray($groups, GroupInterface::ID, GroupInterface::CODE);
    }

    /**
     * Get all customers group
     *
     * @throws LocalizedException
     */
    private function getAllCustomersGroup()
    {
        $allCustomersGroup = $this->groupManagement->getAllCustomersGroup();
        $allCustomersGroup->setCode(__('All Groups'));

        return $allCustomersGroup;
    }
}
