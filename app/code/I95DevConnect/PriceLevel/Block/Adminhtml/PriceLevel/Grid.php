<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Block\Adminhtml\PriceLevel;

/**
 * Customer Group Grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    const HEADER = 'header';
    const INDEX = 'index';
    const HEADER_CSS_CLASS = 'header_css_class';
    const COL_ID = 'col-id';
    const COLUMN_CSS_CLASS = 'column_css_class';
    const TYPE = 'type';

    /**
     * @var \I95DevConnect\PriceLevel\Model\PriceLevelDataFactory
     */
    public $collectionFactory;

    /**
     * Class constructor to include all the dependencies
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \I95DevConnect\PriceLevel\Model\PriceLevelDataFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \I95DevConnect\PriceLevel\Model\PriceLevelDataFactory $collectionFactory,
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
        $this->setId('pricelevelGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
    }

    /**
     * Prepare grid collection object
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->collectionFactory->create()->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare default grid columns
     *
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $columnsData = [
            [
                self::HEADER => __('ID'),
                self::TYPE => 'number',
                self::INDEX => 'pricelevel_id'
            ],
            [
                self::HEADER => __('Price Level'),
                self::TYPE => 'text',
                self::INDEX => 'pricelevel_code',
            ],
            [
                self::HEADER => __('Price Level Description'),
                self::TYPE => 'text',
                self::INDEX => 'description',
            ]
        ];

        foreach ($columnsData as $columnData) {
            $this->addColumn(
                $columnData[self::INDEX],
                [
                    self::HEADER => __($columnData[self::HEADER]),
                    self::TYPE => $columnData['type'],
                    self::INDEX => $columnData[self::INDEX]
                ]
            );
        }

        return $this;
    }
}
