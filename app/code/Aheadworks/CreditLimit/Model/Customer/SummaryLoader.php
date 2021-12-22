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
namespace Aheadworks\CreditLimit\Model\Customer;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\CreditLimit\Api\SummaryRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\CreditLimit\Api\Data\SummaryInterface;

/**
 * Class SummaryLoader
 *
 * @package Aheadworks\CreditLimit\Model\Customer
 */
class SummaryLoader
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SummaryRepositoryInterface
     */
    private $summaryRepository;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SummaryRepositoryInterface $summaryRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SummaryRepositoryInterface $summaryRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->summaryRepository = $summaryRepository;
    }

    /**
     * Load summary list by customer group ID

     * @param int $customerGroupId
     * @return SummaryInterface[]
     * @throws LocalizedException
     */
    public function loadByCustomerGroupId($customerGroupId)
    {
        $this->searchCriteriaBuilder
            ->addFilter('aw_cl_default_summary_by_group_id', $customerGroupId);

        return $this->summaryRepository
            ->getList($this->searchCriteriaBuilder->create())
            ->getItems();
    }
}
