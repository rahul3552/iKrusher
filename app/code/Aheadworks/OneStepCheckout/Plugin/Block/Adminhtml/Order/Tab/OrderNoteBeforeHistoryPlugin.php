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
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Plugin\Block\Adminhtml\Order\Tab;

use Magento\Sales\Block\Adminhtml\Order\View\Tab\History;
use Magento\Backend\Block\Template;

/**
 * Class OrderNoteBeforeHistoryPlugin
 * @package Aheadworks\OneStepCheckout\Plugin\Block\Adminhtml\Order\Tab
 */
class OrderNoteBeforeHistoryPlugin
{
    /**
     * Path to order note template
     */
    const ORDER_NOTE_TEMPLATE = 'Aheadworks_OneStepCheckout::order/tab/history/order_note.phtml';

    /**
     * Add order note html code before history
     *
     * @param $subject
     * @param string $resultHtml
     * @return string
     */
    public function afterToHtml($subject, $resultHtml)
    {
        $orderNoteBlock = $subject->getLayout()->createBlock(Template::class);
        $orderNoteHtml = $orderNoteBlock
            ->setTemplate(self::ORDER_NOTE_TEMPLATE)
            ->setData(['order' => $subject->getOrder()])
            ->toHtml();

        return $orderNoteHtml . $resultHtml;
    }
}
