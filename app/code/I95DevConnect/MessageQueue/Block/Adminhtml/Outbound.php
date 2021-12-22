<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Block\Adminhtml;

/**
 * Outbound Message Queue Grid class
 */
class Outbound extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'Adminhtml_Outbound';
        $this->_blockGroup = 'I95DevConnect_MessageQueue';
        $this->_headerText = __('Custom Grid');
        parent::_construct();
        $this->removeButton('add');
    }
}
