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
 * Interface ItemDataInterface
 * @api
 */
interface ItemDataInterface extends ExtensibleDataInterface
{
    /**
     * #@+
     * Item data values
     */
    const PRODUCT_SKU = 'product_sku';
    const PRODUCT_QTY = 'product_qty';
    const PRODUCT_OPTION = 'product_option';
    /**#@-*/

    /**
     * Set product sku
     *
     * @param string $sku
     * @return $this
     */
    public function setProductSku($sku);

    /**
     * Get product sku
     *
     * @return string
     */
    public function getProductSku();

    /**
     * Set product quantity
     *
     * @param float|int $qty
     * @return $this
     */
    public function setProductQty($qty);

    /**
     * Get product quantity
     *
     * @return float|int
     */
    public function getProductQty();

    /**
     * Set product option
     *
     * @param \Magento\Catalog\Api\Data\ProductOptionInterface|null $productOption
     * @return $this
     */
    public function setProductOption($productOption);

    /**
     * Get product option
     *
     * @return \Magento\Catalog\Api\Data\ProductOptionInterface|null
     */
    public function getProductOption();

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\QuickOrder\Api\Data\ItemDataExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set extension attributes object
     *
     * @param \Aheadworks\QuickOrder\Api\Data\ItemDataExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\QuickOrder\Api\Data\ItemDataExtensionInterface $extensionAttributes
    );
}
