<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Block\Adminhtml\TaxPostingSetup;

/**
 * Block for displaying tax posting setup for business
 */
class Grid extends \I95DevConnect\VatTax\Block\Adminhtml\AbstractGrid
{
    /**
     * @var \I95DevConnect\VatTax\Model\TaxPostingSetupFactory
     */
    protected $collectionFactory;
    const INDEX = 'index';

    /**
     * Tax Posting Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \I95DevConnect\VatTax\Model\TaxPostingSetupFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \I95DevConnect\VatTax\Model\TaxPostingSetupFactory $collectionFactory,
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
        $this->setId('i95DevTaxPostingSetupGrid');
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

    /**
     * Prepare default grid column
     *
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->removeColumn('code');
        $this->removeColumn('description');

        $this->addColumn(
            'tax_busposting_group_code',
            [
            self::HEADER => __('Tax Busposting Group Code'),
            'type' => 'text',
            self::INDEX => 'tax_busposting_group_code',
            ]
        );

        $this->addColumn(
            'tax_productposting_group_code',
            [
            self::HEADER => __('Tax Productposting Group Code'),
            'type' => 'text',
            self::INDEX => 'tax_productposting_group_code'
            ]
        );

        $this->addColumn(
            'tax_percentage',
            [
            self::HEADER => __('Tax Percentage'),
            'type' => 'text',
            self::INDEX => 'tax_percentage'
            ]
        );

        return $this;
    }
}
