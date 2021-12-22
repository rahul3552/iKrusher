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
namespace Aheadworks\Ctq\Model\Comment\History\LogAction;

use Aheadworks\Ctq\Api\Data\CommentInterface;
use Aheadworks\Ctq\Api\Data\HistoryActionInterface;
use Aheadworks\Ctq\Model\Comment;

/**
 * Interface BuilderInterface
 * @package Aheadworks\Ctq\Model\Comment\History\LogAction
 */
interface BuilderInterface
{
    /**
     * Build history action from comment object
     *
     * @param CommentInterface|Comment $comment
     * @return HistoryActionInterface[]
     */
    public function build($comment);
}
