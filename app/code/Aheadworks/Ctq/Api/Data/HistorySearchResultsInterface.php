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
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface HistorySearchResultsInterface
 * @api
 */
interface HistorySearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get history list
     *
     * @return \Aheadworks\Ctq\Api\Data\HistoryInterface[]
     */
    public function getItems();

    /**
     * Set history list
     *
     * @param \Aheadworks\Ctq\Api\Data\HistoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
