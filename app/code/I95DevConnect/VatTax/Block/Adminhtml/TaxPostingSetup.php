<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Block\Adminhtml;

/**
 * Block for removing the add button of tax business setup grid
 */
class TaxPostingSetup extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_TaxPostingSetup';
        $this->_blockGroup = 'I95DevConnect_VatTax';
        $this->_headerText = __('Tax Posting Setup');
        parent::_construct();
        $this->removeButton('add');
    }
}
