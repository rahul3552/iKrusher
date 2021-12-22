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
 * Interface GroupRepositoryInterface
 * @api
 */
interface GroupRepositoryInterface
{
    /**
     * Save group
     *
     * @param \Aheadworks\Ca\Api\Data\GroupInterface $group
     * @return \Aheadworks\Ca\Api\Data\GroupInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\Ca\Api\Data\GroupInterface $group);

    /**
     * Retrieve group by id
     *
     * @param int $groupId
     * @return \Aheadworks\Ca\Api\Data\GroupInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($groupId);

    /**
     * Retrieve group list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Ca\Api\Data\GroupSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
