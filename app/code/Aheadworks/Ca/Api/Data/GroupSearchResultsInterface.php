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
 * @package    Ca
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface GroupSearchResultsInterface
 * @api
 */
interface GroupSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get group list
     *
     * @return \Aheadworks\Ca\Api\Data\GroupInterface[]
     */
    public function getItems();

    /**
     * Set group list
     *
     * @param \Aheadworks\Ca\Api\Data\GroupInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
