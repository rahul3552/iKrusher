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
 * Interface CommentManagementInterface
 * @api
 */
interface CommentManagementInterface
{
    /**
     * Add new comment
     *
     * @param \Aheadworks\Ctq\Api\Data\CommentInterface $comment
     * @return \Aheadworks\Ctq\Api\Data\CommentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addComment(\Aheadworks\Ctq\Api\Data\CommentInterface $comment);

    /**
     * Retrieve attachment
     *
     * @param string $fileName
     * @param int $commentId
     * @param int $quoteId
     * @return \Aheadworks\Ctq\Api\Data\CommentAttachmentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttachment($fileName, $commentId, $quoteId);
}
