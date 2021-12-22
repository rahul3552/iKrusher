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
namespace Aheadworks\CreditLimit\Model\Service;

use Aheadworks\CreditLimit\Api\CustomerManagementInterface;
use Aheadworks\CreditLimit\Api\SummaryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Aheadworks\CreditLimit\Model\Currency\RateConverter;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class CustomerService
 *
 * @package Aheadworks\CreditLimit\Model\Service
 */
class CustomerService implements CustomerManagementInterface
{
    /**
     * @var SummaryRepositoryInterface
     */
    private $summaryRepository;

    /**
     * @var RateConverter
     */
    private $rateConverter;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param SummaryRepositoryInterface $summaryRepository
     * @param RateConverter $rateConverter
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        SummaryRepositoryInterface $summaryRepository,
        RateConverter $rateConverter,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->summaryRepository = $summaryRepository;
        $this->rateConverter = $rateConverter;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @inheritdoc
     */
    public function isCreditLimitAvailable($customerId)
    {
        return $this->getCreditLimitSummary($customerId) ? true : false;
    }

    /**
     * @inheritdoc
     */
    public function isCreditLimitCustom($customerId)
    {
        $summary = $this->getCreditLimitSummary($customerId);
        if ($summary) {
            return $summary->getIsCustomCreditLimit();
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function getCreditLimitAmount($customerId, $currency = null)
    {
        $summary = $this->getCreditLimitSummary($customerId);
        if (!$summary || $summary->getCreditLimit() === null) {
            return null;
        }
        if ($currency) {
            return $this->rateConverter->convertAmount(
                $summary->getCreditLimit(),
                $summary->getCurrency(),
                $currency
            );
        }

        return $this->priceCurrency->round($summary->getCreditLimit());
    }

    /**
     * @inheritdoc
     */
    public function getCreditBalanceAmount($customerId, $currency = null)
    {
        $summary = $this->getCreditLimitSummary($customerId);
        if (!$summary) {
            return 0;
        }
        if ($currency) {
            return $this->rateConverter->convertAmount(
                $summary->getCreditBalance(),
                $summary->getCurrency(),
                $currency
            );
        }

        return $this->priceCurrency->round($summary->getCreditBalance());
    }

    /**
     * @inheritdoc
     */
    public function getCreditAvailableAmount($customerId, $currency = null)
    {
        $summary = $this->getCreditLimitSummary($customerId);
        if (!$summary) {
            return 0;
        }
        if ($currency) {
            return $this->rateConverter->convertAmount(
                $summary->getCreditAvailable(),
                $summary->getCurrency(),
                $currency
            );
        }

        return $this->priceCurrency->round($summary->getCreditAvailable());
    }

    /**
     * Get credit limit summary
     *
     * @param int $customerId
     * @return SummaryInterface|null
     */
    private function getCreditLimitSummary($customerId)
    {
        try {
            $summary = $this->summaryRepository->getByCustomerId($customerId);
            return $summary;
        } catch (NoSuchEntityException $noSuchEntityException) {
            return null;
        }
    }
}
