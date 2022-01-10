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
 * @package     Mageplaza_ProductAttachments
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductAttachments\Ui\Component\Listing\Columns;

use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class CustomerGroup
 * @package Mageplaza\ProductAttachments\Ui\Component\Listing\Columns
 */
class CustomerGroup extends Column
{
    /**
     * @var GroupRepositoryInterface
     */
    protected $_groupRepository;

    /**
     * @var Collection
     */
    protected $_groupCollection;

    /**
     * CustomerGroup constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param GroupRepositoryInterface $groupRepository
     * @param Collection $collection
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        GroupRepositoryInterface $groupRepository,
        Collection $collection,
        array $components = [],
        array $data = []
    ) {
        $this->_groupCollection = $collection;
        $this->_groupRepository = $groupRepository;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->_prepareItem($item, $this->_groupRepository);
            }
        }

        return $dataSource;
    }

    /**
     * @param $dataSource
     *
     * @return array
     */
    public function getCustomerGroupIds($dataSource)
    {
        $customerGroupIds = explode(',', $dataSource);
        $allGroupIds = $this->_groupCollection->getAllIds();
        foreach ($customerGroupIds as $key => $customerGroupId) {
            if (!in_array($customerGroupId, $allGroupIds, true)) {
                unset($customerGroupIds[$key]);
            }
        }

        return $customerGroupIds;
    }

    /**
     * Get customer group name
     *
     * @param array $item
     * @param       $customerGroup
     *
     * @return string
     */
    protected function _prepareItem(array $item, $customerGroup)
    {
        $content = '';
        if (isset($item['customer_group'])) {
            $groupIds = $this->getCustomerGroupIds($item['customer_group']);
            $lastItem = end($groupIds);
            foreach ($groupIds as $groupId) {
                $content .= ($lastItem != $groupId)
                    ? $customerGroup->getById($groupId)->getCode() . ', '
                    : $customerGroup->getById($groupId)->getCode();
            }
        }

        return $content;
    }
}
