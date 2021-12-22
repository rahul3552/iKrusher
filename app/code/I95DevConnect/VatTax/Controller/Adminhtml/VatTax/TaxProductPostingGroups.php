<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Controller\Adminhtml\VatTax;

/**
 * Controller class for displaying tax product posting groups grid
 */
class TaxProductPostingGroups extends AbstractTaxSetup
{

    /**
     * Execite admin action
     * @return bool|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        return $this->setResultPage(
            "I95DevConnect_VatTax::taxProductPostingGroup",
            'I95Dev Tax Product Posting Groups',
            'I95DevConnect\VatTax\Block\Adminhtml\TaxProductPostingGroups'
        );
    }
}
