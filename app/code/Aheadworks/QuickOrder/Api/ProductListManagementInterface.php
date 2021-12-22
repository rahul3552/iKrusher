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
namespace Aheadworks\QuickOrder\Api;

/**
 * Interface ProductListManagementInterface
 * @api
 */
interface ProductListManagementInterface
{
    /**
     * Add new items to list
     *
     * @param int $listId
     * @param \Aheadworks\QuickOrder\Api\Data\ItemDataInterface[] $itemsData
     * @param int $storeId
     * @return \Aheadworks\QuickOrder\Api\Data\OperationResultInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function addItemsToList($listId, $itemsData, $storeId);

    /**
     * Remove all items from list
     *
     * @param int $listId
     * @return \Aheadworks\QuickOrder\Api\Data\OperationResultInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function removeAllItemsFromList($listId);

    /**
     * Update product list item
     *
     * @param string $itemKey
     * @param \Aheadworks\QuickOrder\Api\Data\ItemDataInterface $requestItem
     * @return \Aheadworks\QuickOrder\Api\Data\OperationResultInterface
     * @param int $storeId
     */
    public function updateItem($itemKey, $requestItem, $storeId);

    /**
     * Remove item
     *
     * @param string $itemKey
     * @return \Aheadworks\QuickOrder\Api\Data\OperationResultInterface
     */
    public function removeItem($itemKey);
}
