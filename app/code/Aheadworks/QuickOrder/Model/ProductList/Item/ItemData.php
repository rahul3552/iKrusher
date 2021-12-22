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
namespace Aheadworks\QuickOrder\Model\ProductList\Item;

use Magento\Framework\Api\AbstractExtensibleObject;
use Aheadworks\QuickOrder\Api\Data\ItemDataInterface;

/**
 * Class ItemData
 *
 * @package Aheadworks\QuickOrder\Model\ProductList\Item
 */
class ItemData extends AbstractExtensibleObject implements ItemDataInterface
{
    /**
     * @inheritdoc
     */
    public function getProductSku()
    {
        return  $this->_get(self::PRODUCT_SKU);
    }

    /**
     * @inheritdoc
     */
    public function setProductSku($sky)
    {
        return $this->setData(self::PRODUCT_SKU, $sky);
    }

    /**
     * @inheritdoc
     */
    public function getProductQty()
    {
        return  $this->_get(self::PRODUCT_QTY);
    }

    /**
     * @inheritdoc
     */
    public function setProductQty($qty)
    {
        return $this->setData(self::PRODUCT_QTY, $qty);
    }

    /**
     * @inheritdoc
     */
    public function getProductOption()
    {
        return  $this->_get(self::PRODUCT_OPTION);
    }

    /**
     * @inheritdoc
     */
    public function setProductOption($productOption)
    {
        return $this->setData(self::PRODUCT_OPTION, $productOption);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        \Aheadworks\QuickOrder\Api\Data\ItemDataExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
