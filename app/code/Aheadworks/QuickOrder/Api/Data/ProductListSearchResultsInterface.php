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

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface ProductListSearchResultsInterface
 * @api
 */
interface ProductListSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get product list items
     *
     * @return \Aheadworks\QuickOrder\Api\Data\ProductListInterface[]
     */
    public function getItems();

    /**
     * Set product list items
     *
     * @param \Aheadworks\QuickOrder\Api\Data\ProductListInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
