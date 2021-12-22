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
namespace Aheadworks\CreditLimit\Api;

/**
 * Interface SummaryRepositoryInterface
 * @api
 */
interface SummaryRepositoryInterface
{
    /**
     * Save credit limit summary
     *
     * @param \Aheadworks\CreditLimit\Api\Data\SummaryInterface $creditSummary
     * @return \Aheadworks\CreditLimit\Api\Data\SummaryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\CreditLimit\Api\Data\SummaryInterface $creditSummary);

    /**
     * Retrieve credit limit summary by customer ID
     *
     * @param int $customerId
     * @param bool $reload
     * @return \Aheadworks\CreditLimit\Api\Data\SummaryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCustomerId($customerId, $reload = false);

    /**
     * Retrieve credit limit summary items matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\CreditLimit\Api\Data\SummarySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
