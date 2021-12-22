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
namespace Aheadworks\Ctq\Api;

/**
 * Interface QuoteRepositoryInterface
 * @api
 */
interface QuoteRepositoryInterface
{
    /**
     * Save quote
     *
     * @param \Aheadworks\Ctq\Api\Data\QuoteInterface $quote
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\Ctq\Api\Data\QuoteInterface $quote);

    /**
     * Retrieve quote by ID
     *
     * @param int $quoteId
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($quoteId);

    /**
     * Retrieve quote by cart id
     *
     * @param int $cartId
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCartId($cartId);

    /**
     * Retrieve quote by order id
     *
     * @param int $orderId
     * @return \Aheadworks\Ctq\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByOrderId($orderId);

    /**
     * Retrieve quotes matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Ctq\Api\Data\QuoteSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete quote
     *
     * @param \Aheadworks\Ctq\Api\Data\QuoteInterface $quote
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Aheadworks\Ctq\Api\Data\QuoteInterface $quote);

    /**
     * Delete quote by ID
     *
     * @param int $quoteId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($quoteId);
}
