<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Block\Adminhtml\Product\Edit\Tab;

use I95DevConnect\PriceLevel\Model\ItemPriceListDataFactory;

/**
 * Price List Grid for Product in Admin Login
 * @api
 */

class PriceList extends \Magento\Backend\Block\Widget\Grid\Extended
{

    const HEADER = 'header';
    const INDEX = 'index';
    const HEADER_CSS_CLASS = 'header_css_class';
    const COL_ID = 'col-id';
    const COLUMN_CSS_CLASS = 'column_css_class';
    const PRICELEVEL = 'pricelevel';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    public $productFactory;

    /**
     *
     * @var \I95DevConnect\PriceLevel\Model\ItemPriceListDataFactory
     */
    public $priceListFactory;

    /**
     * Class constructor to include all the dependencies
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param ItemPriceListDataFactory $priceListFactory
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Registry $coreRegistry,
        ItemPriceListDataFactory $priceListFactory,
        array $data = []
    ) {

        $this->productFactory = $productFactory;
        $this->coreRegistry = $coreRegistry;
        $this->priceListFactory = $priceListFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Set grid params
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('pricelist_product_grid');
        $this->setDefaultSort(self::PRICELEVEL);
        $this->setUseAjax(true);
    }

    /**
     * Retrieve currently edited product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('current_product');
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        $collection = $this->priceListFactory->create()->getCollection()
                      ->addFieldToFilter('sku', $this->getProduct()->getSku());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add columns to grid
     *
     * @return $this
     * @throws \Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            self::PRICELEVEL,
            [
            self::HEADER => __('Price Level'),
            'sortable' => true,
            self::INDEX => self::PRICELEVEL,
            self::HEADER_CSS_CLASS => self::COL_ID,
            self::COLUMN_CSS_CLASS => self::COL_ID,
                ]
        );
        $this->addColumn(
            'qty',
            [
            self::HEADER => __('Qty'),
            self::INDEX => 'qty',
            'frame_callback' => [$this, 'formatedQty'],
            self::HEADER_CSS_CLASS => 'col-name',
            self::COLUMN_CSS_CLASS => 'col-name'
                ]
        );

        $this->addColumn(
            'price',
            [
            self::HEADER => __('Price'),
            'type' => 'currency',
            'currency_code' => (string) $this->_scopeConfig->getValue(
                \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
            ),
            self::INDEX => 'price',
            self::HEADER_CSS_CLASS => 'col-type',
            self::COLUMN_CSS_CLASS => 'col-type'
                ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Retrieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('pricelevel/pricelist/grid', ['_current' => true]);
    }

    /**
     * Format quantity display in Grid
     *
     * @param string $value
     * @return string
     */
    public function formatedQty($value)
    {
        return $value . ' ' . __('and above');
    }
}
