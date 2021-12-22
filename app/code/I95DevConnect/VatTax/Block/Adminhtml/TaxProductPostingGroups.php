<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Block\Adminhtml;

/**
 * Block for removing the add button of tax product posting groups grid
 */
class TaxProductPostingGroups extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_TaxProductPostingGroups';
        $this->_blockGroup = 'I95DevConnect_VatTax';
        $this->_headerText = __('Tax Product Posting Groups');
        parent::_construct();
        $this->removeButton('add');
    }
}
