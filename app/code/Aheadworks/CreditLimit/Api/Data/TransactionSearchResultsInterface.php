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
 * @package    CreditLimit
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface TransactionSearchResultsInterface
 * @api
 */
interface TransactionSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get list of transactions
     *
     * @return \Aheadworks\CreditLimit\Api\Data\TransactionInterface[]
     */
    public function getItems();

    /**
     * Set list of transactions
     *
     * @param \Aheadworks\CreditLimit\Api\Data\TransactionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
