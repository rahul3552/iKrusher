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
namespace Aheadworks\CreditLimit\Model\Transaction;

use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface TransactionBuilderInterface
 *
 * @package Aheadworks\CreditLimit\Model\Transaction
 */
interface TransactionBuilderInterface
{
    /**
     * Check if provided parameters are valid for current builder
     *
     * @param TransactionParametersInterface $params
     * @return bool
     * @throws LocalizedException
     */
    public function checkIsValid(TransactionParametersInterface $params);

    /**
     * Fill up transaction object with data
     *
     * @param TransactionInterface $transaction
     * @param TransactionParametersInterface $params
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @return bool
     */
    public function build(TransactionInterface $transaction, TransactionParametersInterface $params);
}
