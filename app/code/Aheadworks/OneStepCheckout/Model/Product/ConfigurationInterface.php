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
namespace Aheadworks\OneStepCheckout\Model\Product;

use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;

/**
 * Interface ConfigurationInterface
 * @package Aheadworks\OneStepCheckout\Model\Product
 */
interface ConfigurationInterface
{
    /**
     * todo: ItemInterface, consider another interface
     * Get options array
     *
     * @param ItemInterface $item
     * @return array
     */
    public function getOptions(ItemInterface $item);

    /**
     * todo: ItemInterface, consider another interface
     * Set options to item
     *
     * @param ItemInterface $item
     * @param array $optionsData
     * @return $this
     */
    public function setOptions(ItemInterface $item, $optionsData = []);
}
