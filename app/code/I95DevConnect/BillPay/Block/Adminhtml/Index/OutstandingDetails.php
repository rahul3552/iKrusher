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
class OutstandingDetails extends \Magento\Backend\Block\Widget\Grid\Extended
{
    const HEADER = 'header';
    const WIDTH = 'width';
    const INDEX = 'index';
    const AMOUNT = 'amount';

    /**
     * @var  \I95DevConnect\BillPay\Model\ArPaymentDetailsFactory
     */
    protected $arPaymentsDetailsFactory;

    /**
     * @var  \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \I95DevConnect\BillPay\Model\ArPaymentDetailsFactory $arPaymentsDetailsFactory
     * @param array $data
     * @param \I95DevConnect\BillPay\Block\Adminhtml\Index\Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \I95DevConnect\BillPay\Model\ArPaymentDetailsFactory $arPaymentsDetailsFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->arPaymentsDetailsFactory = $arPaymentsDetailsFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('billpay_outstandingdetails_grid');
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
        $collection = $this->arPaymentsDetailsFactory->create()->getCollection()
            ->addFieldToFilter('target_invoice_id', $this->getRequest()->getParam('invoice_id'));
        $collection->getSelect()->joinLeft(
            ['i95dev_ar_payment' => $collection->getTable('i95dev_ar_payment')],
            'main_table.payment_id = i95dev_ar_payment.primary_id',
            [
                'i95dev_ar_payment.payment_type',
                'i95dev_ar_payment.payment_date',
                'i95dev_ar_payment.cash_receipt_number'
            ]
        );
        $collection->addFieldToFilter('main_table.status', 'paid');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare column for billpay details
     */
    protected function _prepareColumns()
    {
        $this->addColumn('payment_type', [self::HEADER => __('Payment Type'),
            self::WIDTH => '100', self::INDEX => 'payment_type']);
        $this->addColumn('payment_date', [self::HEADER => __('Payment Date'),
            self::WIDTH => '100', self::INDEX => 'payment_date']);
        $this->addColumn('primary_id', [self::HEADER => __('Payment Id'),
            self::WIDTH => '100', self::INDEX => 'primary_id']);
        $this->addColumn(
            'cash_receipt_number',
            [self::HEADER => __('Cash Receipt Number'), self::INDEX => 'cash_receipt_number']
        );
        $this->addColumn(
            self::AMOUNT,
            [
                self::HEADER => __('amount'),
                self::INDEX => self::AMOUNT,
                'currency' => self::AMOUNT,
                'type' => 'currency',
                'currency_code' => $this->storeManager->getStore()->getCurrentCurrency()->getCode()
            ]
        );

        $this->addColumn(
            'status',
            [
                self::HEADER => __('Status'),
                self::INDEX => 'status',
                'frame_callback' => [$this, 'formattedStatus']
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
