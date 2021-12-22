<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 * @codingStandardsIgnoreFile
 */

namespace I95DevConnect\BillPay\Block\Customer;

use \Magento\Framework\Exception\LocalizedException;
use \Magento\Customer\Model\Session;

/**
 * Sales order history block
 */
class ManagePayment extends \Magento\Framework\View\Element\Template
{

    const STATUS_PENDING = 'pending';
    const ORDER_STATUS = 'order_status';
    const INVOICE = 'invoice';
    const PENALTY = 'penalty';
    const OUTSTANDING_AMOUNT = 'outstanding_amount';
    const INVOICE_AMOUNT = 'invoice_amount';


    /**
     * @var string
     */
    protected $_template = 'I95DevConnect_BillPay::customer/managepayment.phtml';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected $payments;

    /**
     * @var CollectionFactoryInterface
     */
    private $paymentCollectionFactory;
    protected $paymentInterface;
    protected $paymentFactory;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentHelper;

    /**
     * Factory for payment method models
     *
     * @var \Magento\Payment\Model\Method\Factory
     */
    protected $methodFactory;
    protected $scopeConfig;
    protected $payment;
    protected $orderBillingBlock;
    protected $orderBillingForm;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    public $dataObject;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \I95DevConnect\BillPay\Model\ArbookFactory $paymentCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Payment\Api\PaymentMethodListInterface $paymentInterface
     * @param \Magento\Payment\Model\Method\InstanceFactory $paymentFactory
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Magento\Payment\Model\Method\Factory $methodFactory
     * @param \Magento\Quote\Model\Quote\Payment $payment
     * @param \Magento\Sales\Block\Adminhtml\Order\Create\Billing\Method $orderBillingBlock
     * @param \Magento\Sales\Block\Adminhtml\Order\Create\Billing\Method\Form $orderBillingForm
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \I95DevConnect\BillPay\Model\ArbookFactory $paymentCollectionFactory,
        Session $customerSession,
        \Magento\Payment\Api\PaymentMethodListInterface $paymentInterface,
        \Magento\Payment\Model\Method\InstanceFactory $paymentFactory,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Payment\Model\Method\Factory $methodFactory,
        \Magento\Quote\Model\Quote\Payment $payment,
        \Magento\Sales\Block\Adminhtml\Order\Create\Billing\Method $orderBillingBlock,
        \Magento\Sales\Block\Adminhtml\Order\Create\Billing\Method\Form $orderBillingForm,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\DataObject $dataObject,
        array $data = []
    ) {
        $this->paymentCollectionFactory = $paymentCollectionFactory;
        $this->customerSession = $customerSession;
        $this->paymentFactory = $paymentFactory;
        $this->paymentHelper = $paymentHelper;
        $this->methodFactory = $methodFactory;
        $this->scopeConfig = $context->getScopeConfig();
        $this->payment = $payment;
        $this->paymentInterface = $paymentInterface;
        $this->orderBillingBlock = $orderBillingBlock;
        $this->orderBillingForm = $orderBillingForm;
        $this->pricingHelper = $pricingHelper;
        $this->dataObject = $dataObject;

        parent::__construct($context, $data);
    }

    /**
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getPayments()
    {
        if (!($customerId = $this->customerSession->getCustomerId())) {
            return false;
        }

        if (!$this->payments) {
            $this->payments = $this->paymentCollectionFactory->create();
            $this->payments = $this->payments->getCollection()->addFieldToSelect(
                '*'
            )->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter(self::ORDER_STATUS, self::STATUS_PENDING)
            ->addFieldToFilter('type', [self::INVOICE,self::PENALTY])
            ->addFieldToFilter(
                self::OUTSTANDING_AMOUNT,
                ['gt' => 0]
            );
        }

        return $this->payments;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getPayments()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'billpay.managepayment.pager'
            )->setCollection(
                $this->getPayments()
            );

            $this->setChild('pager', $pager);
            $this->getPayments()->load();
        }

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
                    throw new LocalizedException('Payment model name is not provided in config!');
                }

                $this->methodFactory->create($class);
                $method->setInfoInstance($this->payment);
                if ($method->getCode() == 'payflowpro') {
                    $block = $this->_layout->createBlock(\Magento\Payment\Block\Form\Cc::class, $method->getCode());
                    $block->setMethod($method);
                    $this->setChild(
                        'payment.method.' . $method->getCode(),
                        $block
                    );
                } else {
                    $this->setChild(
                        'payment.method.' . $method->getCode(),
                        $this->paymentHelper->getMethodFormBlock($method, $this->_layout)
                    );
                }
            } catch (LocalizedException $e) {
                throw new LocalizedException(__($e->getMessage()));
            }
        }

        return $this;
    }

    protected function getMethodModelConfigName($code)
    {
        return sprintf('%s/%s/model', 'payment', $code);
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
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }

    public function formatPrice($price)
    {
        return $this->pricingHelper->currency($price, true, false);
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
        foreach ($this->getPayments() as $item) {
            foreach ($fields as $field => $value) {
                $fields[$field]+=$item->getData($field);
            }
        }

        $totals->setData($fields);

        return $totals;
    }

    /**
     * Retrieve available payment methods
     *
     * @return array
     */
    public function getPaymentMethods()
    {
        $store = null;
        $methods = [];
        $class = $this->scopeConfig->getValue(
            "i95devconnect_billpay/billpay_enabled_settings/payment_frontend",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($class != null) {
            $configMethods = explode(",", $class);
            foreach ($this->paymentInterface->getActiveList($store) as $method) {
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

    /**
     * Calculating all pending invoices total amount
     * @Return float $invoiceTotal
     */
    public function getTotalInvoceAmount()
    {
        $accountReceivableCollection = $this->payments;
        $invoiceTotal = 0;
        foreach ($accountReceivableCollection as $data) {
            $invoiceStatus = isset($data[self::ORDER_STATUS]) ? $data[self::ORDER_STATUS] : '';
            if ($invoiceStatus == self::STATUS_PENDING) {
                $this->updateTotal($data,$invoiceTotal,self::INVOICE_AMOUNT);
            }
        }

        return $invoiceTotal;
    }

    /**
     * Getting Account Receivables collection filtered by customer id
     */
    public function getARCollection()
    {
        try {
            if (!($customerId = $this->customerSession->getCustomerId())) {
                return false;
            }

            $arData = $this->paymentCollectionFactory->create();
            $arData = $arData->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter(self::ORDER_STATUS, ['eq' => self::STATUS_PENDING])
                ->addFieldToFilter(self::OUTSTANDING_AMOUNT, ['gt' => 0]);
        } catch (LocalizedException $exc) {
            throw new LocalizedException(__($exc->getMessage()));
        }

        return $arData;
    }

    /**
     * Calculating total outstanding amount
     * @Return float $outStandingTotal
     */
    public function getTotalOutStandingAmount()
    {
        $accountReceivableCollection = $this->payments;
        $outStandingTotal = 0;
        foreach ($accountReceivableCollection as $data) {
            $invoiceStatus = isset($data[self::ORDER_STATUS]) ? $data[self::ORDER_STATUS] : '';
            if ($invoiceStatus == self::STATUS_PENDING) {
                $this->updateTotal($data,$outStandingTotal,self::OUTSTANDING_AMOUNT);
            }
        }

        return $outStandingTotal;
    }

    public function updateTotal($data,&$t,$k)
    {
        $amount = isset($data[$k]) ? $data[$k] : '';
        if ($data['type'] == self::INVOICE
            || $data['type'] == 'debitmemo'
            || $data['type'] == self::PENALTY
            || $data['type'] == 'servicerepair'
            || $data['type'] == 'warranty'
        ) {
            $t = $t + $amount;
        }
        if ($data['type'] == 'return' || $data['type'] == 'creditmemo') {
            $t = $t - $amount;
        }
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
