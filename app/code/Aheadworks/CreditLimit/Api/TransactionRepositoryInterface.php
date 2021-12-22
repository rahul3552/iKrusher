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
 * Transaction CRUD interface
 * @api
 */
interface TransactionRepositoryInterface
{
    /**
     * Save transaction data
     *
     * @param \Aheadworks\CreditLimit\Api\Data\TransactionInterface $transaction
     * @return \Aheadworks\CreditLimit\Api\Data\TransactionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\CreditLimit\Api\Data\TransactionInterface $transaction);

    /**
     * Retrieve transaction data by ID
     *
     * @param  int $transactionId
     * @return \Aheadworks\CreditLimit\Api\Data\TransactionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($transactionId);

    /**
     * Retrieve transactions matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\CreditLimit\Api\Data\TransactionSearchResultsInterface;
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
