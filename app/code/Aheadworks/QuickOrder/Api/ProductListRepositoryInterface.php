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
 * @package    QuickOrder
 * @version    1.0.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Api;

/**
 * Interface ProductListRepositoryInterface
 * @api
 */
interface ProductListRepositoryInterface
{
    /**
     * Retrieve list by its ID
     *
     * @param int $listId
     * @return \Aheadworks\QuickOrder\Api\Data\ProductListInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($listId);

    /**
     * Retrieve list by customer ID
     *
     * @param int $customerId
     * @return \Aheadworks\QuickOrder\Api\Data\ProductListInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCustomerId($customerId);

    /**
     * Save list
     *
     * @param \Aheadworks\QuickOrder\Api\Data\ProductListInterface $list
     * @return \Aheadworks\QuickOrder\Api\Data\ProductListInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\QuickOrder\Api\Data\ProductListInterface $list);

    /**
     * Retrieve list items matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\QuickOrder\Api\Data\ProductListSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
