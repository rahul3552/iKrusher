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
 * Interface ExternalQuoteManagementInterface
 * @api
 */
interface ExternalQuoteManagementInterface
{
    /**
     * Retrieve quote by ID
     *
     * @param int $quoteId
     * @return \Aheadworks\Ctq\Api\Data\ExternalQuoteInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($quoteId);

    /**
     * Retrieve quotes matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Ctq\Api\Data\ExternalQuoteInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Make a quote duplication
     *
     * @param int $quoteId
     * @return \Aheadworks\Ctq\Api\Data\ExternalQuoteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function copyQuote($quoteId);
}
