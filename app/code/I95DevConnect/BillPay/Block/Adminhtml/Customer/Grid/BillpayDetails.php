<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Block\Adminhtml\Customer\Grid;

use \Magento\Customer\Controller\RegistryConstants;

/**
 * Adminhtml customer manage payment grid block
 */
class BillpayDetails extends \Magento\Backend\Block\Widget\Grid\Extended
{
    const HEADER = 'header';
    const WIDTH='width';
    const INDEX = 'index';
    const FRAME_CALLBACK = 'frame_callback';
    const INVOICE_AMOUNT = 'invoice_amount';
    const CURRENCY = "currency";
    const OUTSTANDING_AMOUNT = "outstanding_amount";
    const DISCOUNT_AMOUNT = "discount_amount";
    const CURRENCY_CODE = "currency_code";
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
        $this->setId('customer_billpaydetails_grid');
        $this->setDefaultSort('target_invoive_id', 'desc');
        $this->setUseAjax(true);
    }

    /**
     * Apply various selection filters to prepare the billpay details grid collection.
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->collectionFactory->getReport('i95devconnect_billpay_payment_grid_data_source')
            ->addFieldToFilter(
                'customer_id',
                $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)
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
            'magento_order_id',
            [self::HEADER => __('Order Number'), self::WIDTH => '100', self::INDEX => 'magento_order_id']
        );
        $this->addColumn('type', [self::HEADER => __('Type'), self::WIDTH => '100', self::INDEX => 'type']);
        $this->addColumn(
            'target_invoice_id',
            [self::HEADER => __('Document Number'), self::WIDTH => '100', self::INDEX => 'target_invoice_id']
        );
        $this->addColumn(
            'modified_date',
            [self::HEADER => __('Document Date'),
                self::INDEX => 'modified_date',
                self::FRAME_CALLBACK => [$this, 'formattedDate']]
        );
        $this->addColumn(
            'due_date',
            [self::HEADER => __('Document Due Date'),
                self::INDEX => 'due_date',
                self::FRAME_CALLBACK => [$this, 'formattedDate']]
        );

        $this->addColumn(
            self::INVOICE_AMOUNT,
            [
                self::HEADER => __('Document Amount'),
                self::INDEX => self::INVOICE_AMOUNT,
                self::CURRENCY => self::INVOICE_AMOUNT,
                'type' => self::CURRENCY,
                self::CURRENCY_CODE => $this->storeManager->getStore()->getCurrentCurrency()->getCode()
            ]
        );

        $this->addColumn(
            self::OUTSTANDING_AMOUNT,
            [
                self::HEADER => __('Outstanding Amount'),
                self::INDEX => self::OUTSTANDING_AMOUNT,
                self::CURRENCY => self::OUTSTANDING_AMOUNT,
                'type' => self::CURRENCY,
                self::CURRENCY_CODE => $this->storeManager->getStore()->getCurrentCurrency()->getCode()
            ]
        );

        $this->addColumn(
            self::DISCOUNT_AMOUNT,
            [
                self::HEADER => __('Discount Amount'),
                self::INDEX => self::DISCOUNT_AMOUNT,
                self::CURRENCY => self::DISCOUNT_AMOUNT,
                'type' => self::CURRENCY,
                self::CURRENCY_CODE => $this->storeManager->getStore()->getCurrentCurrency()->getCode()
            ]
        );

        $this->addColumn(
            'order_status',
            [
                self::HEADER => __('Document Status'),
                self::INDEX => 'order_status',
                self::FRAME_CALLBACK => [$this, 'formattedStatus']
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

    /**
     * format status
     * @return string
     */
    public function formattedDate($value, $row, $column, $isExport)
    {
        $date = date_create($value);
        return date_format($date, 'F j, Y');
    }
}
