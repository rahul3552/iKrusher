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
namespace Aheadworks\QuickOrder\Model;

use Magento\Framework\Model\AbstractModel;
use Aheadworks\QuickOrder\Api\Data\ProductListInterface;
use Aheadworks\QuickOrder\Model\ResourceModel\ProductList as ProductListResource;

/**
 * Class ProductList
 *
 * @package Aheadworks\QuickOrder\Model
 */
class ProductList extends AbstractModel implements ProductListInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ProductListResource::class);
    }

    /**
     * @inheritdoc
     */
    public function getListId()
    {
        return $this->getData(ProductListInterface::LIST_ID);
    }

    /**
     * @inheritdoc
     */
    public function setListId($listId)
    {
        return $this->setData(ProductListInterface::LIST_ID, $listId);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId()
    {
        return $this->getData(ProductListInterface::CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(ProductListInterface::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(ProductListInterface::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(ProductListInterface::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritdoc
     */
    public function getItems()
    {
        return $this->getData(ProductListInterface::ITEMS);
    }

    /**
     * @inheritdoc
     */
    public function setItems($items)
    {
        return $this->setData(ProductListInterface::ITEMS, $items);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        \Aheadworks\QuickOrder\Api\Data\ProductListExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
