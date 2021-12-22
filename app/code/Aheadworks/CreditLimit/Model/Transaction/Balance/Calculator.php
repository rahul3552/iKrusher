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
namespace Aheadworks\CreditLimit\Model\Transaction\Balance;

use Aheadworks\CreditLimit\Model\Currency\RateConverter;

/**
 * Class Calculator
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Balance
 */
class Calculator
{
    /**
     * @var RateConverter
     */
    private $rateConverter;

    /**
     * @param RateConverter $rateConverter
     */
    public function __construct(
        RateConverter $rateConverter
    ) {
        $this->rateConverter = $rateConverter;
    }

    /**
     * Calculate new credit balance
     *
     * @param float $creditBalance
     * @param string $creditCurrency
     * @param float $amount
     * @param string $amountCurrency
     * @return float
     * @throws \Exception
     */
    public function calculateCreditBalance($creditBalance, $creditCurrency, $amount, $amountCurrency)
    {
        $amount = $this->rateConverter->convertAmount($amount, $amountCurrency, $creditCurrency);
        return $creditBalance + $amount;
    }

    /**
     * Calculate new available credit
     *
     * @param float $creditBalance
     * @param float $creditLimit
     * @return float
     */
    public function calculateAvailableCredit($creditBalance, $creditLimit)
    {
        return $creditBalance + $creditLimit;
    }

    /**
     * Calculate currency rate between two currencies
     *
     * @param string $currencyFrom
     * @param string $currencyTo
     * @return float
     */
    public function calculateRate($currencyFrom, $currencyTo)
    {
        return $this->rateConverter->getRate($currencyFrom, $currencyTo);
    }
}
