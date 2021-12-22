<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_VatTax
 */

namespace I95DevConnect\VatTax\Block\Adminhtml;

/**
 * Block for displaying grid of tax business posting groups for customers
 */
abstract class AbstractGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    const HEADER = 'header';
    const INDEX = 'index';
    const HEADER_CSS_CLASS = 'header_css_class';
    const COL_ID = 'col-id';
    const COLUMN_CSS_CLASS = 'column_css_class';

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
    }

    /**
     * Prepare grid collection object
     *
     * @return $this
     */
    protected function prepareTaxCollection($collectionFactory)
    {
        $collection = $collectionFactory
            ->create()
            ->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare default grid column
     *
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns() // NOSONAR
    {
        parent::_prepareColumns();

        $columnArr = [
            [
                self::HEADER => __('ID'),
                'type' => 'number',
                self::INDEX => 'id'
            ],
            [
                self::HEADER => __('Code'),
                'type' => 'text',
                self::INDEX => 'code',
            ],
            [
                self::HEADER => __('Description'),
                'type' => 'text',
                self::INDEX => 'description',
            ]
        ];

        foreach ($columnArr as $column) {
            $this->addColumn(
                $column[self::INDEX],
                [
                    self::HEADER => __($column[self::HEADER]),
                    'type' => $column['type'],
                    self::INDEX => $column[self::INDEX]
                ]
            );

        }

        return $this;
    }
}
