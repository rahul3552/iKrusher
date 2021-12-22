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
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Api;

/**
 * Interface HistoryRepositoryInterface
 * @api
 */
interface HistoryRepositoryInterface
{
    /**
     * Save history
     *
     * @param \Aheadworks\Ctq\Api\Data\HistoryInterface $history
     * @return \Aheadworks\Ctq\Api\Data\HistoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\Ctq\Api\Data\HistoryInterface $history);

    /**
     * Retrieve history by id
     *
     * @param int $historyId
     * @return \Aheadworks\Ctq\Api\Data\HistoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($historyId);

    /**
     * Retrieve history matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Ctq\Api\Data\HistorySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
