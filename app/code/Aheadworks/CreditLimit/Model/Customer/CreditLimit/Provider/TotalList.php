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

use Aheadworks\CreditLimit\Api\CustomerManagementInterface;
use Aheadworks\CreditLimit\Api\SummaryRepositoryInterface;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class TotalList
 *
 * @package Aheadworks\CreditLimit\Model\Customer\CreditLimit\Provider
 */
class TotalList implements ProviderInterface
{
    /**
     * @var CustomerManagementInterface
     */
    private $customerManagement;

    /**
     * @var SummaryRepositoryInterface
     */
    private $summaryRepository;

    /**
     * @var bool
     */
    private $includeCurrencyRate;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param CustomerManagementInterface $customerManagement
     * @param SummaryRepositoryInterface $summaryRepository
     * @param StoreManagerInterface $storeManager
     * @param bool $includeCurrencyRate
     */
    public function __construct(
        CustomerManagementInterface $customerManagement,
        SummaryRepositoryInterface $summaryRepository,
        StoreManagerInterface $storeManager,
        $includeCurrencyRate = false
    ) {
        $this->customerManagement = $customerManagement;
        $this->summaryRepository = $summaryRepository;
        $this->storeManager = $storeManager;
        $this->includeCurrencyRate = $includeCurrencyRate;
    }

    /**
     * @inheritdoc
     */
    public function getData($customerId, $websiteId)
    {
        $data['totals'] = $this->getTotals($customerId, null);
        if (!$this->includeCurrencyRate) {
            return $data;
        }

        $summary = $this->summaryRepository->getByCustomerId($customerId);
        $currentCurrency = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
        if ($summary->getCurrency() != $currentCurrency) {
            $data['totals'] = array_merge(
                $data['totals'],
                $this->getTotals($customerId, $currentCurrency)
            );
        }

        return $data;
    }

    /**
     * Get totals
     *
     * @param int $customerId
     * @param string|null $currency
     * @return array
     */
    private function getTotals($customerId, $currency)
    {
        $suffix = $currency ? '_converted' : '';
        return [
            SummaryInterface::CREDIT_LIMIT . $suffix  =>
                $this->customerManagement->getCreditLimitAmount($customerId, $currency),
            SummaryInterface::CREDIT_AVAILABLE . $suffix =>
                $this->customerManagement->getCreditAvailableAmount($customerId, $currency),
            SummaryInterface::CREDIT_BALANCE . $suffix =>
                $this->customerManagement->getCreditBalanceAmount($customerId, $currency)
        ];
    }
}
