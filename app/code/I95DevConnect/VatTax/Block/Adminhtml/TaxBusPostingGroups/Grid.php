<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Block\Adminhtml\TaxBusPostingGroups;

use I95DevConnect\VatTax\Block\Adminhtml\AbstractGrid;

/**
 * Block for displaying grid of tax business posting groups for customers
 */
class Grid extends AbstractGrid
{
    /**
     * @var \I95DevConnect\VatTax\Model\TaxBusPostingGroupsFactory
     */
    protected $collectionFactory;

    /**
     * Tax Bus Posting Groups Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \I95DevConnect\VatTax\Model\TaxBusPostingGroupsFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \I95DevConnect\VatTax\Model\TaxBusPostingGroupsFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('i95DevTaxBusPostingGroupsGrid');
    }

    /**
     * Prepare grid collection object
     *
     * @return \I95DevConnect\VatTax\Block\Adminhtml\TaxProductPostingGroups\Grid
     */
    protected function _prepareCollection()
    {
        return $this->prepareTaxCollection($this->collectionFactory);
    }
}
