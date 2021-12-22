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
namespace Aheadworks\QuickOrder\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ProductListItemInterface
 * @api
 */
interface ProductListItemInterface extends ExtensibleDataInterface
{
    /**
     * #@+
     * Constants for keys of data array.
     * Identical to the name of the getter in snake case
     */
    const ITEM_ID = 'item_id';
    const ITEM_KEY = 'item_key';
    const LIST_ID = 'list_id';
    const PRODUCT_ID = 'product_id';
    const PRODUCT_NAME = 'product_name';
    const PRODUCT_TYPE = 'product_type';
    const PRODUCT_SKU = 'product_sku';
    const PRODUCT_QTY = 'product_qty';
    const PRODUCT_OPTION = 'product_option';
    /**#@-*/

    /**
     * Get item ID
     *
     * @return int
     */
    public function getItemId();

    /**
     * Set item ID
     *
     * @param int $itemId
     * @return $this
     */
    public function setItemId($itemId);

    /**
     * Get item key
     *
     * @return string
     */
    public function getItemKey();

    /**
     * Set item key
     *
     * @param string $itemKey
     * @return $this
     */
    public function setItemKey($itemKey);

    /**
     * Get list ID
     *
     * @return int
     */
    public function getListId();

    /**
     * Set list ID
     *
     * @param int $listId
     * @return $this
     */
    public function setListId($listId);

    /**
     * Get product ID
     *
     * @return int
     */
    public function getProductId();

    /**
     * Set product ID
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * Get product name
     *
     * @return string
     */
    public function getProductName();

    /**
     * Set product name
     *
     * @param string $productName
     * @return $this
     */
    public function setProductName($productName);

    /**
     * Get product type
     *
     * @return string
     */
    public function getProductType();

    /**
     * Set product type
     *
     * @param string $productType
     * @return $this
     */
    public function setProductType($productType);

    /**
     * Get product sku
     *
     * @return string
     */
    public function getProductSku();

    /**
     * Set product sku
     *
     * @param string $setSku
     * @return $this
     */
    public function setProductSku($setSku);

    /**
     * Get product quantity
     *
     * @return float
     */
    public function getProductQty();

    /**
     * Set product qty
     *
     * @param float $qty
     * @return $this
     */
    public function setProductQty($qty);

    /**
     * Get product option
     *
     * @return \Magento\Catalog\Api\Data\ProductOptionInterface|null
     */
    public function getProductOption();

    /**
     * Set product option
     *
     * @param \Magento\Catalog\Api\Data\ProductOptionInterface $productOption
     * @return $this
     */
    public function setProductOption($productOption);

    /**
     * Retrieve existing extension attributes object if exists
     *
     * @return \Aheadworks\QuickOrder\Api\Data\ProductListItemExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\QuickOrder\Api\Data\ProductListItemExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\QuickOrder\Api\Data\ProductListItemExtensionInterface $extensionAttributes
    );
}
