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
namespace Aheadworks\Ctq\Model;

use Aheadworks\Ctq\Api\Data\CommentInterface;
use Aheadworks\Ctq\Api\HistoryRepositoryInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Aheadworks\Ctq\Model\History\Notifier;
use Aheadworks\Ctq\Model\Quote\ToHistory as QuoteToHistory;
use Aheadworks\Ctq\Model\Comment\ToHistory as CommentToHistory;
use Psr\Log\LoggerInterface;

/**
 * Class HistoryService
 * @package Aheadworks\Ctq\Model
 */
class HistoryManagement
{
    /**
     * @var HistoryRepositoryInterface
     */
    private $historyRepository;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var QuoteToHistory
     */
    private $quoteToHistory;

    /**
     * @var CommentToHistory
     */
    private $commentToHistory;

    /**
     * @var Notifier
     */
    private $notifier;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param HistoryRepositoryInterface $historyRepository
     * @param QuoteRepositoryInterface $quoteRepository
     * @param QuoteToHistory $quoteToHistory
     * @param CommentToHistory $commentToHistory
     * @param Notifier $notifier
     * @param LoggerInterface $logger
     */
    public function __construct(
        HistoryRepositoryInterface $historyRepository,
        QuoteRepositoryInterface $quoteRepository,
        QuoteToHistory $quoteToHistory,
        CommentToHistory $commentToHistory,
        Notifier $notifier,
        LoggerInterface $logger
    ) {
        $this->historyRepository = $historyRepository;
        $this->quoteRepository = $quoteRepository;
        $this->quoteToHistory = $quoteToHistory;
        $this->commentToHistory = $commentToHistory;
        $this->notifier = $notifier;
        $this->logger = $logger;
    }

    /**
     * Add quote changes to history log
     *
     * @param QuoteInterface|Quote $quote
     * @return void
     */
    public function addQuoteToHistory($quote)
    {
        try {
            $history = $this->quoteToHistory->convert($quote);
            if ($history) {
                $history = $this->historyRepository->save($history);
                $this->notifier->notify($history, $quote);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }

    /**
     * Add comment to history log
     *
     * @param CommentInterface|Comment $comment
     * @return void
     */
    public function addCommentToHistory($comment)
    {
        try {
            $history = $this->commentToHistory->convert($comment);
            if ($history) {
                $history = $this->historyRepository->save($history);
                $quote = $this->quoteRepository->get($comment->getQuoteId());
                $this->notifier->notify($history, $quote);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }
}
