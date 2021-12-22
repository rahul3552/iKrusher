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
class ManagePayments extends \Magento\Backend\Block\Widget\Grid\Extended
{

    const STATUS_PENDING = 'pending';
    const ORDER_STATUS = 'order_status';
    const INVOICE_AMOUNT = 'invoice_amount';
    const HEADER = 'header';
    const WIDTH = 'width';
    const CURRENCY = 'currency';
    const CURRENCY_CODE = 'currency_code';
    const NODISPLAY = 'no-display';
    const HEADER_CSS_CLASS = 'header_css_class';
    const DISCOUNT_AMOUNT = 'discount_amount';
    const DISCOUNT_DT = 'discount_dt';
    const OUTSTANDING_AMOUNT = 'outstanding_amount';
    const INTEREST_AMOUNT = 'interest_amount';
    const INDEX = 'index';
    const COLUMN_CSS_CLASS = 'column_css_class';
    const TOTALS_LABEL = 'totals_label';
    const FILTER = 'filter';

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
    public $countTotals = true;
    public $currencySymbol;
    protected $_template = 'I95DevConnect_BillPay::grid.phtml';
    public $orderBillingBlock;
    public $orderBillingForm;
    public $paymentInterface;
    public $paymentFactory;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    public $paymentHelper;

    /**
     * Factory for payment method models
     *
     * @var \Magento\Payment\Model\Method\Factory
     */
    public $methodFactory;
    public $scopeConfig;
    public $payment;
    public $dataObject;

    /**
     * @var  \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Sales\Block\Adminhtml\Order\Create\Billing\Method $orderBillingBlock
     * @param \Magento\Sales\Block\Adminhtml\Order\Create\Billing\Method\Form $orderBillingForm
     * @param \Magento\Payment\Api\PaymentMethodListInterface $paymentInterface
     * @param \Magento\Payment\Model\Method\InstanceFactory $paymentFactory
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Magento\Payment\Model\Method\Factory $methodFactory
     * @param \Magento\Quote\Model\Quote\Payment $payment
     * @param \Magento\Framework\DataObject $dataObject
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Sales\Block\Adminhtml\Order\Create\Billing\Method $orderBillingBlock,
        \Magento\Sales\Block\Adminhtml\Order\Create\Billing\Method\Form $orderBillingForm,
        \Magento\Payment\Api\PaymentMethodListInterface $paymentInterface,
        \Magento\Payment\Model\Method\InstanceFactory $paymentFactory,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Payment\Model\Method\Factory $methodFactory,
        \Magento\Quote\Model\Quote\Payment $payment,
        \Magento\Framework\DataObject $dataObject,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->collectionFactory = $collectionFactory;
        $this->currency = $currency;
        $this->orderBillingBlock = $orderBillingBlock;
        $this->orderBillingForm = $orderBillingForm;
        $this->paymentInterface = $paymentInterface;
        $this->paymentFactory = $paymentFactory;
        $this->paymentHelper = $paymentHelper;
        $this->methodFactory = $methodFactory;
        $this->scopeConfig = $context->getScopeConfig();
        $this->payment = $payment;
        $this->dataObject = $dataObject;
        $this->storeManager = $storeManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customer_managepayment_grid');
        $this->setDefaultSort('target_invoive_id', 'desc');
        $this->setUseAjax(true);
    }

    /**
     * Apply various selection filters to prepare the sales order grid collection.
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->collectionFactory->getReport('i95devconnect_billpay_payment_grid_data_source')
            ->addFieldToFilter(
                'customer_id',
                $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)
            )
            ->addFieldToFilter(self::OUTSTANDING_AMOUNT, ['gt' => 0])
            ->addFieldToFilter(self::ORDER_STATUS, self::STATUS_PENDING);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * get total of column
     * @return object Dataobject
     */
    public function getTotals()
    {
        $totals = $this->dataObject;
        $fields = [
            self::INVOICE_AMOUNT => 0,
            self::OUTSTANDING_AMOUNT => 0
        ];
        foreach ($this->getCollection() as $item) {
            if ($item->getData('type') == 'invoice'
                || $item->getData('type') == 'debitmemo'
                || $item->getData('type') == 'penalty'
                || $item->getData('type') == 'servicerepair'
                || $item->getData('type') == 'warranty'
            ) {
                foreach ($fields as $field => $value) {
                    $fields[$field]+=$item->getData($field);
                }
            }

            if ($item->getData('type') == 'return' || $item->getData('type') == 'creditmemo') {
                foreach ($fields as $field => $value) {
                    $fields[$field]-=$item->getData($field);
                }
            }
        }
        $fields['customer_id'] = $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        $totals->setData($fields);

        return $totals;
    }

    /**
     * Prepare children blocks
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        /**
         * Create child blocks for payment methods forms
         */
        foreach ($this->getPaymentMethods() as $method) {
            try {
                $class = $this->scopeConfig->getValue(
                    $this->getMethodModelConfigName($method->getCode()),
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );

                if (!$class) {
                    throw new \UnexpectedValueException('Payment model name is not provided in config!');
                }

                $this->methodFactory->create($class);
                $method->setInfoInstance($this->payment);
                $this->setChild(
                    'payment.method.' . $method->getCode(),
                    $this->paymentHelper->getMethodFormBlock($method, $this->_layout)
                );
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * @param string $code
     * @return string
     */
    protected function getMethodModelConfigName($code)
    {
        return sprintf('%s/%s/model', 'payment', $code);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {

        $this->addColumn(
            'check_invoice',
            [
                'type' => 'radio',
                'html_name' => 'check_invoice',
                self::HEADER => __(''),
                'align' => 'center',
                self::INDEX => '',
                self::TOTALS_LABEL => __('Total'),
                self::FILTER => false,
                self::COLUMN_CSS_CLASS => "i95dev_invoice",
            ]
        );

        $this->addColumn(
            'magento_order_id',
            [   self::HEADER => __('Order Number'),
                self::WIDTH => '100', self::INDEX => 'magento_order_id',
                self::FILTER => false
            ]
        );
        $this->addColumn(
            'type',
            [
                self::HEADER => __('Document Type'),
                self::WIDTH => '100',
                self::INDEX => 'type',
                self::FILTER => false
            ]
        );

        $this->addColumn(
            'target_invoice_id',
            [self::HEADER => __('Document Number'),
                self::WIDTH => '100', self::INDEX => 'target_invoice_id', self::FILTER => false]
        );

        $this->addColumn(
            'modified_date',
            [self::HEADER => __('Document Date'), self::INDEX => 'modified_date', self::FILTER => false]
        );

        $this->addColumn(
            'due_date',
            [self::HEADER => __('Document Due Date'), self::INDEX => 'due_date', self::FILTER => false]
        );

        $this->addColumn(
            self::INVOICE_AMOUNT,
            [
                self::HEADER => __('Document Amount'),
                self::INDEX => self::INVOICE_AMOUNT,
                self::CURRENCY => self::INVOICE_AMOUNT,
                self::FILTER => false,
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
                self::FILTER => false,
                'type' => self::CURRENCY,
                self::CURRENCY_CODE => $this->storeManager->getStore()->getCurrentCurrency()->getCode()
            ]
        );

        $this->addColumn(
            self::INTEREST_AMOUNT,
            [
                self::HEADER => __('Interest Amount'),
                self::INDEX => self::INTEREST_AMOUNT,
                self::CURRENCY => self::INTEREST_AMOUNT,
                self::TOTALS_LABEL => __(''),
                self::FILTER => false,
                'type' => self::CURRENCY,
                self::CURRENCY_CODE => $this->storeManager->getStore()->getCurrentCurrency()->getCode(),
                self::COLUMN_CSS_CLASS=>self::NODISPLAY,
                self::HEADER_CSS_CLASS=>self::NODISPLAY
            ]
        );

        $this->addColumn(
            self::DISCOUNT_AMOUNT,
            [
                self::HEADER => __('Discount Amount'),
                self::INDEX => self::DISCOUNT_AMOUNT,
                self::CURRENCY => self::DISCOUNT_AMOUNT,
                self::TOTALS_LABEL => __(''),
                self::FILTER => false,
                'type' => self::CURRENCY,
                self::CURRENCY_CODE => $this->storeManager->getStore()->getCurrentCurrency()->getCode()
            ]
        );

        $this->addColumn(
            self::DISCOUNT_DT,
            [
                self::HEADER => __('Discount Date'),
                self::INDEX => self::DISCOUNT_DT,
                self::FILTER => false,
                self::COLUMN_CSS_CLASS=>self::NODISPLAY,
                self::HEADER_CSS_CLASS=>self::NODISPLAY
            ]
        );

        $this->addColumn(
            self::DISCOUNT_DT,
            [
                self::HEADER => __('Discount Date'),
                self::INDEX => self::DISCOUNT_DT,
                self::FILTER => false,
                self::COLUMN_CSS_CLASS=>self::NODISPLAY,
                self::HEADER_CSS_CLASS=>self::NODISPLAY
            ]
        );

        $this->addColumn(
            self::ORDER_STATUS,
            [
                self::HEADER => __('Document Status'),
                self::INDEX => self::ORDER_STATUS,
                self::FILTER => false
            ]
        );

        $this->addColumn(
            'pay_now',
            [
                self::HEADER => __('Pay Now'),
                'type' => 'input',
                'name' => 'pay_now',
                'align' => 'center',
                self::COLUMN_CSS_CLASS => "i95dev_click",
                self::WIDTH => '20px',
                self::TOTALS_LABEL => __(''),
                self::FILTER => false
            ]
        );

        return parent::_prepareColumns();
    }

    public function getMainButtonsHtml()
    {
        $html = parent::getMainButtonsHtml(); //get the parent class buttons
        $addButton = $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Button::class)
            ->setData([
                'label' => 'Pay now',
                'onclick' => "showDiv()",
                'class' => 'primary ui-corner-all',
                'id' => 'paynowbutton'
            ])->toHtml();
        return $addButton . $html;
    }

    public function orderBillingBlock()
    {
        return $this->orderBillingBlock;
    }

    public function orderBillingForm()
    {
        return $this->orderBillingForm;
    }

    /**
     * Retrieve available payment methods
     *
     * @return array
     */
    public function getPaymentMethods()
    {
        $methods = [];
        $class = $this->scopeConfig->getValue(
            "i95devconnect_billpay/billpay_enabled_settings/payment_backend",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($class != null) {
            $configMethods = explode(",", $class);
            foreach ($this->paymentInterface->getActiveList(null) as $method) {
                $methodInstance = $this->paymentFactory->create($method);
                $class = $this->scopeConfig->getValue(
                    $this->getMethodModelConfigName($methodInstance->getCode()),
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );

                if ($class && in_array($methodInstance->getCode(), $configMethods)) {
                    $methods[] = $methodInstance;
                }
            }
        }

        return $methods;
    }

    public function getCurrencySymbol()
    {
        return $this->currency->getCurrencySymbol();
    }

    /**
     * Get form action
     * @return type string
     */
    public function getPaymentFormAction()
    {
        return $this->getUrl('billpay/index/post', ['_secure' => true]);
    }

    public function hasMethods()
    {
        $condition = false;
        $methods = [];
        $class = $this->scopeConfig->getValue(
            "i95devconnect_billpay/billpay_enabled_settings/payment_backend",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $configMethods = explode(",", $class);
        foreach ($this->paymentInterface->getActiveList(null) as $method) {
            $methodInstance = $this->paymentFactory->create($method);

            if (in_array($methodInstance->getCode(), $configMethods)) {
                $methods[] = $methodInstance;
            }
        }

        if (!empty($methods)) {
            $condition = true;
        }

        return $condition;
    }
}
