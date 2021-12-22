<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Block\Adminhtml\TaxProductPostingGroups;

use I95DevConnect\VatTax\Block\Adminhtml\AbstractGrid;

/**
 * Block for displaying grid of tax product posting groups for products
 */
class Grid extends AbstractGrid
{
    /**
     * @var \I95DevConnect\VatTax\Model\TaxProductPostingGroupsFactory
     */
    public $collectionFactory;

    /**
     * Tax Product Posting Groups constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \I95DevConnect\VatTax\Model\TaxProductPostingGroupsFactory $collectionFactory
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \I95DevConnect\VatTax\Model\TaxProductPostingGroupsFactory $collectionFactory,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Prepare grid collection object
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        return $this->prepareTaxCollection($this->collectionFactory);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('i95DevTaxProductPostingGroupsGrid');
    }
}
