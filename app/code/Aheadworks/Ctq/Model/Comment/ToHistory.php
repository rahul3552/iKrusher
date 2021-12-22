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
namespace Aheadworks\Ctq\Model\Comment;

use Aheadworks\Ctq\Api\Data\CommentInterface;
use Aheadworks\Ctq\Api\Data\HistoryInterface;
use Aheadworks\Ctq\Api\Data\HistoryInterfaceFactory;
use Aheadworks\Ctq\Model\Comment\History\LogAction\Builder;
use Aheadworks\Ctq\Model\Source\History\Status;

/**
 * Class ToHistory
 * @package Aheadworks\Ctq\Model\Comment
 */
class ToHistory
{
    /**
     * @var HistoryInterfaceFactory
     */
    private $historyFactory;

    /**
     * @var Builder
     */
    private $historyActionBuilder;

    /**
     * @param HistoryInterfaceFactory $historyFactory
     * @param Builder $historyActionBuilder
     */
    public function __construct(
        HistoryInterfaceFactory $historyFactory,
        Builder $historyActionBuilder
    ) {
        $this->historyFactory = $historyFactory;
        $this->historyActionBuilder = $historyActionBuilder;
    }

    /**
     * Create history log from comment
     *
     * @param CommentInterface $comment
     * @return HistoryInterface|null
     */
    public function convert($comment)
    {
        $history = null;
        $commentHistoryActions = $this->historyActionBuilder->build($comment);
        if ($commentHistoryActions) {
            /** @var HistoryInterface $history */
            $history = $this->historyFactory->create();
            $history
                ->setQuoteId($comment->getQuoteId())
                ->setOwnerType($comment->getOwnerType())
                ->setOwnerId($comment->getOwnerId())
                ->setStatus(Status::UPDATED_QUOTE)
                ->setActions($commentHistoryActions);
        }
        return $history;
    }
}
