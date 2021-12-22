<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Block\Adminhtml\Customer\Grid;

use \Magento\Customer\Controller\RegistryConstants;

/**
 * Customer edit billpay payment grid block
 */
class BillpayPayments extends \Magento\Backend\Block\Widget\Grid\Extended
{

    const HEADER = 'header';
    const WIDTH='width';
    const INDEX = 'index';
    const TOTAL_AMT = 'total_amt';
    const PRIMARY_ID = 'primary_id';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * @var  \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory
     */
    public $collectionFactory;

    /**
     * @var  \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customer_billpaypayment_grid');
        $this->setDefaultSort('desc');
        $this->setUseAjax(true);
    }

    /**
     * Apply various selection filters to prepare the sales order grid collection.
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->collectionFactory->getReport('billpay_paymentdetails_grid_data_source')
            ->addFieldToFilter(
                'customer_id',
                $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)
            );
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn(self::PRIMARY_ID, [self::HEADER => __('Payment ID'),
            self::WIDTH => '100', self::INDEX => self::PRIMARY_ID]);
        $this->addColumn('payment_type', [self::HEADER => __('Payment Type'),
            self::WIDTH => '100', self::INDEX => 'payment_type']);
        $this->addColumn(
            'payment_trans_id',
            [self::HEADER => __('Transaction ID'), self::WIDTH => '100', self::INDEX => 'payment_trans_id']
        );
        $this->addColumn(
            'cash_receipt_number',
            [self::HEADER => __('Cash Receipt Number'), self::WIDTH => '100', self::INDEX => 'cash_receipt_number']
        );
        $this->addColumn(
            'payment_date',
            [self::HEADER => __('Payment Date'), self::INDEX => 'payment_date', 'type' => 'date']
        );
        $this->addColumn(
            self::TOTAL_AMT,
            [
                self::HEADER => __('Amount'),
                self::INDEX => self::TOTAL_AMT,
                'currency' => self::TOTAL_AMT,
                'type' => 'currency',
                'currency_code' => $this->storeManager->getStore()->getCurrentCurrency()->getCode()
            ]
        );
        $this->addColumn(
            'status',
            [self::HEADER => __('Status'), self::INDEX => 'status']
        );
        $this->addColumn(
            'view',
            [
                self::HEADER => __('View'),
                'type' => 'action',
                'getter' => 'getPrimaryId',
                'actions' => [
                    [
                        'caption' => __('View'),
                        'url' => [
                            'base' => 'billpay/index/paymentview',
                        ],
                        'field' => self::PRIMARY_ID
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                self::INDEX => self::PRIMARY_ID,
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Retrieve the Url for a specified billpayment row.
     *
     * @param \Magento\Sales\Model\Order|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return false;
    }
}
