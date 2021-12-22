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
use Aheadworks\Ctq\Api\Data\HistoryActionInterfaceFactory;
use Aheadworks\Ctq\Model\Source\History\Action\Type as ActionType;
use Aheadworks\Ctq\Model\Source\History\Action\Status as ActionStatus;

/**
 * Class CommentBuilder
 * @package Aheadworks\Ctq\Model\Comment\History\LogAction
 */
class CommentBuilder implements BuilderInterface
{
    /**
     * @var HistoryActionInterfaceFactory
     */
    private $historyActionFactory;

    /**
     * @param HistoryActionInterfaceFactory $historyActionFactory
     */
    public function __construct(HistoryActionInterfaceFactory $historyActionFactory)
    {
        $this->historyActionFactory = $historyActionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function build($comment)
    {
        $historyActions = [];
        /** @var HistoryActionInterface $historyAction */
        $historyAction = $this->historyActionFactory->create();
        $historyAction
            ->setType(ActionType::COMMENT)
            ->setStatus(ActionStatus::CREATED)
            ->setOldValue($comment->getComment())
            ->setValue($comment->getId())
            ->setActions($this->getAttachmentActions($comment));
        $historyActions[] = $historyAction;
        return $historyActions;
    }

    /**
     * Retrieve attachment actions
     *
     * @param CommentInterface $comment
     * @return array
     */
    private function getAttachmentActions($comment)
    {
        $attachmentHistoryActions = [];
        if ($attachments = $comment->getAttachments()) {
            foreach ($attachments as $attachment) {
                /** @var HistoryActionInterface $historyAction */
                $historyAction = $this->historyActionFactory->create();
                $historyAction
                    ->setType(ActionType::COMMENT_ATTACHMENT)
                    ->setStatus(ActionStatus::CREATED)
                    ->setName($attachment->getName())
                    ->setValue($attachment->getFileName());
                $attachmentHistoryActions[] = $historyAction;
            }
        }
        return $attachmentHistoryActions;
    }
}
