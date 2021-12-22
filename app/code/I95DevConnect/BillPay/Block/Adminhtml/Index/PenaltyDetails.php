<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Block\Adminhtml\Index;

/**
 * Adminhtml customer manage payment grid block
 */
class PenaltyDetails extends \Magento\Backend\Block\Widget\Grid\Extended
{
    const HEADER = 'header';
    const INDEX = 'index';

    /**
     * @var  \I95DevConnect\BillPay\Model\ArPenaltyDetailsFactory
     */
    protected $arPenaltyDetailsFactory;

    /**
     * @var  \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \I95DevConnect\BillPay\Model\ArPenaltyDetailsFactory $arPenaltyDetailsFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \I95DevConnect\BillPay\Model\ArPenaltyDetailsFactory $arPenaltyDetailsFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->arPenaltyDetailsFactory = $arPenaltyDetailsFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('billpay_penaltydetails_grid');
        $this->setUseAjax(true);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    /**
     * Apply various selection filters to prepare the billpay details grid collection.
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->arPenaltyDetailsFactory->create()->getCollection()
            ->addFieldToFilter('main_table.penalty_id', $this->getRequest()->getParam('invoice_id'));
        $collection->getSelect()->joinLeft(
            ['i95dev_ar_penalty' => $collection->getTable('i95dev_ar_penalty')],
            'main_table.penalty_id = i95dev_ar_penalty.penalty_id',
            ['i95dev_ar_penalty.term']
        );

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare column for billpay details
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'reference_type',
            [
                self::HEADER => __('Reference Type'),
                'width' => '100',
                self::INDEX => 'reference_type',
                'frame_callback' => [$this, 'formattedStatus']
            ]
        );
        $this->addColumn('reference_id', [self::HEADER => __('Reference Id'),
            'width' => '100',
            self::INDEX => 'reference_id']);
        $this->addColumn(
            'reference_amount',
            [
                self::HEADER => __('Reference Amount'),
                self::INDEX => 'reference_amount',
                'type' => 'currency',
                'currency_code' => $this->storeManager->getStore()->getCurrentCurrency()->getCode()
            ]
        );
        $this->addColumn(
            'amount',
            [
                self::HEADER => __('Amount'),
                self::INDEX => 'amount',
                'type' => 'currency',
                'currency_code' => $this->storeManager->getStore()->getCurrentCurrency()->getCode()
            ]
        );
        $this->addColumn(
            'term',
            [
                self::HEADER => __('Term'),
                self::INDEX => 'term'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * format status
     * @return string
     */
    public function formattedStatus($value, $row, $column, $isExport)
    {
        return ucfirst($value);
    }
}
