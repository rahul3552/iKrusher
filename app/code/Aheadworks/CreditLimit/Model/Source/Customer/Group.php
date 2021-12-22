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
namespace Aheadworks\CreditLimit\Model\Source\Customer;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\Convert\DataObject;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SortOrder;

/**
 * Class Group
 *
 * @package Aheadworks\CreditLimit\Model\Source\Customer
 */
class Group implements OptionSourceInterface
{
    /**
     * @var GroupManagementInterface
     */
    private $groupManagement;

    /**
     * @var DataObject
     */
    private $objectConverter;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @param GroupManagementInterface $groupManagement
     * @param DataObject $objectConverter
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        GroupManagementInterface $groupManagement,
        DataObject $objectConverter,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->groupManagement = $groupManagement;
        $this->objectConverter = $objectConverter;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $sortOrderData = $this->sortOrderBuilder->getData();

        $groups = $this->groupManagement->getLoggedInGroups();

        // fix for magento bug with SortOrderBuilder
        if (!empty($sortOrderData)) {
            $this->sortOrderBuilder
                ->setField($sortOrderData[SortOrder::FIELD])
                ->setDirection($sortOrderData[SortOrder::DIRECTION]);
        }

        return $this->objectConverter->toOptionArray($groups, GroupInterface::ID, GroupInterface::CODE);
    }
}
