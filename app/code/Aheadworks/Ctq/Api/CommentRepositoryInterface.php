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
 * Interface CommentRepositoryInterface
 * @api
 */
interface CommentRepositoryInterface
{
    /**
     * Save comment
     *
     * @param \Aheadworks\Ctq\Api\Data\CommentInterface $comment
     * @return \Aheadworks\Ctq\Api\Data\CommentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\Ctq\Api\Data\CommentInterface $comment);

    /**
     * Retrieve comment by id
     *
     * @param int $commentId
     * @return \Aheadworks\Ctq\Api\Data\CommentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($commentId);

    /**
     * Retrieve comment matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Ctq\Api\Data\CommentSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
