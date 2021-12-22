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
namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\NegotiationTabs;

use Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\AbstractEdit;
use Aheadworks\Ctq\Api\Data\HistoryInterface;
use Aheadworks\Ctq\Block\History\Render;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class History
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\NegotiationTabs
 */
class History extends AbstractEdit
{
    /**
     * Return the history item html
     *
     * @param HistoryInterface $history
     * @return string
     * @throws LocalizedException
     */
    public function getHistoryItemHtml($history)
    {
        /** @var Render $block */
        $block = $this->getLayout()->getBlock('aw_ctq.quote.history.render');
        $block->setHistory($history);

        return $block->toHtml();
    }
}
