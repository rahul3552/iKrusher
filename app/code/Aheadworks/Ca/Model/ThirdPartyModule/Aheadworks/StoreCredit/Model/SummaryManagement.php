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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Model;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class SummaryManagement
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Model
 */
class SummaryManagement
{
    /**
     * @var \Aheadworks\StoreCredit\Api\TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var \Aheadworks\StoreCredit\Api\SummaryRepositoryInterface
     */
    private $summaryRepository;

    /**
     * @var StoreCreditManagement
     */
    private $storeCreditManagement;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param StoreCreditManagement $storeCreditManagement
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        PriceCurrencyInterface $priceCurrency,
        StoreCreditManagement $storeCreditManagement
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->storeCreditManagement = $storeCreditManagement;
        $this->transactionRepository =
            $objectManager->get(\Aheadworks\StoreCredit\Api\TransactionRepositoryInterface::class);
        $this->summaryRepository =
            $objectManager->get(\Aheadworks\StoreCredit\Api\SummaryRepositoryInterface::class);
    }

    /**
     * Get current customer balance by transaction ID
     *
     * @param int $transactionId
     * @param int $balance
     * @return int
     */
    public function getCurrentCustomerBalanceByTransactionId($transactionId, $balance)
    {
        try {
            $transaction = $this->transactionRepository->getById($transactionId);
        } catch (\Exception $exception) {
            $transaction = null;
        }
        $balance = $transaction
            ? $this->getCurrentCustomerBalance($transaction->getCustomerId(), $balance)
            : $balance;

        return $balance;
    }

    /**
     * Get current customer balance
     *
     * @param int $customerId
     * @param float|int $balance
     * @return float
     */
    public function getCurrentCustomerBalance($customerId, $balance = 0)
    {
        try {
            $summary = $this->summaryRepository->get($customerId);
        } catch (\Exception $exception) {
            $summary = null;
        }
        $balance = $summary ? $summary->getBalance() : $balance;

        return $balance;
    }

    /**
     * Get customer points balance in base currency
     *
     * @param CustomerInterface $customer
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrentCustomerBalanceBaseCurrency($customer)
    {
        $summary = $this->summaryRepository->get($customer->getId());
        $balance = $summary->getBalance();
        $baseCurrencyBalance = $this->priceCurrency->format(
            $this->priceCurrency->convertAndRound($balance),
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $customer->getStoreId()
        );

        return $baseCurrencyBalance;
    }
}
