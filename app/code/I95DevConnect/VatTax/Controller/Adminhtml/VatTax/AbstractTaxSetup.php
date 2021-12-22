<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Controller\Adminhtml\VatTax;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Controller class for displaying tax product posting groups grid
 */
abstract class AbstractTaxSetup extends \Magento\Backend\App\Action
{

    public $resultPageFactory;

    /**
     * @var \I95DevConnect\VatTax\Helper\Data
     */
    public $vatTaxHelper;

    /**
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \I95DevConnect\VatTax\Helper\Data $vatTaxHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \I95DevConnect\VatTax\Helper\Data $vatTaxHelper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->vatTaxHelper = $vatTaxHelper;
    }

    /**
     * To Check the resources
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('I95DevConnect_VatTax::vattax');
    }

    /**
     * @param $menu
     * @param $title
     * @param $blockClass
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function setResultPage($menu, $title, $blockClass)
    {
        $isEnabled = $this->vatTaxHelper->isVatTaxEnabled();
        if (!$isEnabled) {
            return false;
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu($menu);
        $resultPage->getConfig()->getTitle()->prepend(__($title));
        $resultPage->addBreadcrumb(__('I95Dev Vat Tax'), __($title));
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock($blockClass)
        );

        return $resultPage;
    }
}
