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
namespace Aheadworks\Ca\Api;

/**
 * Interface CompanyRepositoryInterface
 * @api
 */
interface CompanyRepositoryInterface
{
    /**
     * Save company
     *
     * @param \Aheadworks\Ca\Api\Data\CompanyInterface $comapny
     * @return \Aheadworks\Ca\Api\Data\CompanyInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\Ca\Api\Data\CompanyInterface $company);

    /**
     * Retrieve company by id
     *
     * @param int $companyId
     * @return \Aheadworks\Ca\Api\Data\CompanyInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($companyId);

    /**
     * Retrieve company list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Ca\Api\Data\CompanySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
