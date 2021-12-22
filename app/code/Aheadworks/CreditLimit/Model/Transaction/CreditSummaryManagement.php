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

use Aheadworks\CreditLimit\Api\SummaryRepositoryInterface;
use Aheadworks\CreditLimit\Api\Data\SummaryInterfaceFactory;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class CreditSummaryManagement
 *
 * @package Aheadworks\CreditLimit\Model\Transaction
 */
class CreditSummaryManagement
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var SummaryInterfaceFactory
     */
    private $summaryInterfaceFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var SummaryRepositoryInterface
     */
    private $summaryRepository;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param SummaryInterfaceFactory $summaryInterfaceFactory
     * @param StoreManagerInterface $storeManager
     * @param SummaryRepositoryInterface $summaryRepository
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        SummaryInterfaceFactory $summaryInterfaceFactory,
        StoreManagerInterface $storeManager,
        SummaryRepositoryInterface $summaryRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->summaryInterfaceFactory = $summaryInterfaceFactory;
        $this->storeManager = $storeManager;
        $this->summaryRepository = $summaryRepository;
    }

    /**
     * Prepare credit summary
     *
     * @param int $customerId
     * @param bool $reload
     * @return SummaryInterface
     * @throws LocalizedException
     */
    public function getCreditSummary($customerId, $reload = false)
    {
        try {
            $summary = $this->summaryRepository->getByCustomerId($customerId, $reload);
        } catch (NoSuchEntityException $noSuchEntityException) {
            $summary = $this->summaryInterfaceFactory->create();
            $customer = $this->customerRepository->getById($customerId);
            $summary->setCustomerId($customer->getId());
            $summary->setWebsiteId($customer->getWebsiteId());
        }

        if (!$summary->getCurrency()) {
            $this->resolveCurrency($customerId, $summary);
        }

        return $summary;
    }

    /**
     * Save credit summary
     *
     * @param SummaryInterface $creditSummary
     * @return SummaryInterface
     * @throws LocalizedException
     */
    public function saveCreditSummary($creditSummary)
    {
        return $this->summaryRepository->save($creditSummary);
    }

    /**
     * Resolve summary currency
     *
     * @param int $customerId
     * @param SummaryInterface $summary
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function resolveCurrency($customerId, $summary)
    {
        $customer = $this->customerRepository->getById($customerId);
        $website = $this->storeManager->getWebsite($customer->getWebsiteId());
        $summary->setCustomerId($customer->getId());
        $summary->setWebsiteId($customer->getWebsiteId());
        $summary->setCurrency($website->getBaseCurrencyCode());
    }
}
