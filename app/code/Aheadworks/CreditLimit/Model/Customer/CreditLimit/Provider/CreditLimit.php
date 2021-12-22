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
namespace Aheadworks\CreditLimit\Model\Customer\CreditLimit\Provider;

use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\CreditLimit\Api\SummaryRepositoryInterface;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class CreditLimit
 *
 * @package Aheadworks\CreditLimit\Model\Customer\CreditLimit\Provider
 */
class CreditLimit implements ProviderInterface
{
    /**
     * @var SummaryRepositoryInterface
     */
    private $summaryRepository;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param SummaryRepositoryInterface $summaryRepository
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        SummaryRepositoryInterface $summaryRepository,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->summaryRepository = $summaryRepository;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @inheritdoc
     */
    public function getData($customerId, $websiteId)
    {
        try {
            $summary = $this->summaryRepository->getByCustomerId($customerId);
            $creditLimit = $summary->getCreditLimit() !== null
                ? $this->priceCurrency->round($summary->getCreditLimit())
                : null;
            $data[SummaryInterface::CUSTOMER_ID] = $summary->getCustomerId();
            $data[SummaryInterface::WEBSITE_ID] = $summary->getWebsiteId();
            $data[SummaryInterface::CREDIT_LIMIT] = $creditLimit;
            $data[SummaryInterface::COMPANY_ID] = $summary->getCompanyId();
            $data[SummaryInterface::IS_CUSTOM_CREDIT_LIMIT] = !$summary->getCreditLimit()
                || $summary->getIsCustomCreditLimit();
        } catch (NoSuchEntityException $exception) {
            $data[SummaryInterface::CUSTOMER_ID] = 'not_set';
            $data[SummaryInterface::COMPANY_ID] = 'not_set';
        }

        return $data;
    }
}
