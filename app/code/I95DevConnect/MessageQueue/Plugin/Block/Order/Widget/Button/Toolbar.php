<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Plugin\Block\Order\Widget\Button;

use Magento\Backend\Block\Widget\Button\Toolbar as ToolbarContext;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Backend\Block\Widget\Button\ButtonList;
use \Magento\Store\Model\ScopeInterface;

/**
 * Tool bar Edit
 */
class Toolbar
{

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $data;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * Toolbar constructor.
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $data
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $data,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->data = $data;
        $this->storeManager = $storeManager;
    }

    /**
     * To remove ship and invoice buttons in tool bar
     * @param ToolbarContext $toolbar
     * @param AbstractBlock $context
     * @param ButtonList $buttonList
     * @return array
     */
    public function beforePushButtons(
        ToolbarContext $toolbar, //NOSONAR
        \Magento\Framework\View\Element\AbstractBlock $context,
        \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
    ) {
        if (!$context instanceof \Magento\Sales\Block\Adminhtml\Order\View) {
            return [$context, $buttonList];
        }
        $component = $this->data->scopeConfig->getValue(
            'i95dev_messagequeue/I95DevConnect_settings/component',
            ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );
        if ($this->data->isEnabled() && $component == "AX" || $component == "NAV"
            || $component == "GP" || $component == "BC" || $component == "Sage") {
            $buttonList->remove('order_ship');
            $buttonList->remove('order_invoice');
        }

        return [$context, $buttonList];
    }
}
