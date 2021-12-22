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
namespace Aheadworks\Ctq\Observer;

use Aheadworks\Ctq\Model\QuoteList\MergeProcessor;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class MergeQuoteListsObserver
 * @package Aheadworks\Ctq\Observer
 */
class MergeQuoteListsObserver implements ObserverInterface
{
    /**
     * @var MergeProcessor
     */
    private $mergeProcessor;

    /**
     * @param MergeProcessor $mergeProcessor
     */
    public function __construct(
        MergeProcessor $mergeProcessor
    ) {
        $this->mergeProcessor = $mergeProcessor;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(Observer $observer)
    {
        $this->mergeProcessor->mergeQuotes();
    }
}
