<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Controller\Adminhtml\VatTax;

/**
 * Controller class for displaying tax business posting groups grid
 */
class TaxBusPostingGroups extends AbstractTaxSetup
{

    /**
     * Execute action
     * @return bool|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        return $this->setResultPage(
            'I95DevConnect_VatTax::taxBusPostingGroup',
            'I95Dev Tax Bus Posting Groups',
            'I95DevConnect\VatTax\Block\Adminhtml\TaxBusPostingGroups'
        );
    }
}
