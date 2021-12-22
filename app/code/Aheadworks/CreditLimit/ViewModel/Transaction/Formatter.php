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
namespace Aheadworks\CreditLimit\ViewModel\Transaction;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Aheadworks\CreditLimit\Model\Source\Transaction\Action as ActionSource;
use Aheadworks\CreditLimit\Model\Transaction\Balance\Formatter as BalanceFormatter;

/**
 * Class Formatter
 *
 * @package Aheadworks\CreditLimit\ViewModel\Transaction
 */
class Formatter implements ArgumentInterface
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var ActionSource
     */
    private $actionSource;

    /**
     * @var BalanceFormatter
     */
    private $balanceFormatter;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     * @param ActionSource $actionSource
     * @param BalanceFormatter $balanceFormatter
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        ActionSource $actionSource,
        BalanceFormatter $balanceFormatter
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->actionSource = $actionSource;
        $this->balanceFormatter = $balanceFormatter;
    }

    /**
     * Format price
     *
     * @param float $price
     * @param string $currencyCode
     * @return string
     */
    public function formatPrice($price, $currencyCode)
    {
        return $this->priceCurrency->format(
            $price,
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            null,
            $currencyCode
        );
    }

    /**
     * Format transaction action
     *
     * @param string $action
     * @return string
     */
    public function formatTransactionAction($action)
    {
        return $this->actionSource->getActionLabel($action);
    }

    /**
     * Format transaction amount
     *
     * @param array $transaction
     * @return string
     */
    public function formatTransactionAmount($transaction)
    {
        return $this->balanceFormatter->formatTransactionAmount($transaction);
    }
}
