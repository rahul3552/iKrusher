<?php

/**
 * @author    i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package   I95DevConnect_CancelOrder
 */

namespace I95DevConnect\CancelOrder\Plugin\Block\Order\Widget\Button;

use Magento\Backend\Block\Widget\Button\Toolbar as ToolbarContext;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Backend\Block\Widget\Button\ButtonList;

/**
 * plugin file added to remove cancel button on sales order edit page
 */
class Toolbar
{

    public $data;

    /**
     * Toolbar constructor.
     *
     * @param \I95DevConnect\CancelOrder\Helper\Data $data
     */
    public function __construct(\I95DevConnect\CancelOrder\Helper\Data $data)
    {
        $this->data = $data;
    }

    /**
     * Removes cancel order button from sales order edit page
     *
     * @param  ToolbarContext $toolbar
     * @param  AbstractBlock  $context
     * @param  ButtonList     $buttonList
     * @return array
     */
    public function beforePushButtons(
        ToolbarContext $toolbar, //NOSONAR
        \Magento\Framework\View\Element\AbstractBlock $context,
        \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
    ) {
        if ($this->data->isEnabled()) {
            if (!$context instanceof \Magento\Sales\Block\Adminhtml\Order\View) {
                return [$context, $buttonList];
            }

            $buttonList->remove('order_cancel');
        }

        return [$context, $buttonList];
    }
}
