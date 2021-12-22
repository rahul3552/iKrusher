<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Block\Adminhtml\Widget;

use Magento\Backend\Block\Template\Context;

/**
 * Class for i95Dev Entity list
 */
class Entitylist extends \Magento\Backend\Block\Template
{
    protected $_template = 'I95DevConnect_MessageQueue::widget/entitylist.phtml';
    public $items = [];
    public $msgData;

    /**
     *
     * @param Context $context
     * @param \I95DevConnect\MessageQueue\Helper\Data $msgData
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \I95DevConnect\MessageQueue\Helper\Data $msgData
    ) {
        parent::__construct($context);
        $this->msgData = $msgData;
    }

    /**
     * get entity details
     * @return array
     */
    public function getEntities()
    {
        return $this->msgData->getSyncEntities();
    }
}
