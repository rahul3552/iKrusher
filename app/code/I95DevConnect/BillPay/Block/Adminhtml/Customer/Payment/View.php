<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Block\Adminhtml\Customer\Payment;

use \Magento\Customer\Controller\RegistryConstants;
use \Magento\Backend\Model\Auth\Session;

/**
 * BillPay details view payment
 */
class View extends \Magento\Backend\Block\Widget\Form\Container
{

    const PAYMENT_ID = 'payment_id';
    const LABEL = 'label';
    const CLASSL = 'class';
    const ONCLICK = 'onclick';
    const PRIMARY_ID = 'primary_id';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * @var \I95DevConnect\Billpay\Model\ArPaymentFactory
     */
    private $paymentCollectionFactory;
    public $payments;

    /**
     * Backend session
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    public $backendSession;
    public $scopeConfig;

    /**
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param Session $backendSession
     * @param \Magento\Framework\Registry $registry
     * @param \I95DevConnect\BillPay\Model\ArPaymentFactory $paymentCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        Session $backendSession,
        \Magento\Framework\Registry $registry,
        \I95DevConnect\BillPay\Model\ArPaymentFactory $paymentCollectionFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->backendSession = $backendSession;
        $this->paymentCollectionFactory = $paymentCollectionFactory;
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $data);
    }

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_objectId = self::PAYMENT_ID;
        $this->_blockGroup = 'I95DevConnect_BillPay';
        $this->_controller = 'adminhtml_customer_payment';
        $this->_mode = 'view';
        $this->_session = $this->backendSession;

        parent::_construct();

        $this->buttonList->remove('save');
        $this->buttonList->remove('back');
        $this->buttonList->remove('reset');

        $this->addButton(
            'send_notification',
            [
                self::LABEL => __('Send Email'),
                self::CLASSL => 'send-email',
                self::ONCLICK => 'confirmSetLocation(\'' . __(
                    'Are you sure you want to send a billpay email to customer?'
                ) . '\', \'' . $this->getEmailUrl() . '\')'
            ]
        );

        $this->buttonList->add(
            'print',
            [
                self::LABEL => __('Print'),
                self::CLASSL => 'print',
                self::ONCLICK => 'setLocation(\'' . $this->getPrintUrl() . '\')'
            ]
        );

        $this->addButton(
            'back',
            [
                self::LABEL => __('Back'),
                self::ONCLICK => 'setLocation(\'' . $this->getUrl(
                    'customer/index/edit',
                    [
                        'id' => $this->getCustomerId()
                    ]
                ) . '\')',
                self::CLASSL => 'back'
            ],
            -1
        );
    }

    /**
     * Retrieve email url
     *
     * @return string
     */
    public function getEmailUrl()
    {
        return $this->getUrl(
            'billpay/index/email',
            [
                self::PAYMENT_ID => $this->_request->getParam(self::PRIMARY_ID),
                'customer_id' => $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)
            ]
        );
    }

    /**
     * Get print url
     *
     * @return string
     */
    public function getPrintUrl()
    {
        return $this->getUrl('billpay/index/print', [self::PAYMENT_ID => $this->_request->getParam(self::PRIMARY_ID)]);
    }

    /**
     * Get Payement details
     */
    public function getPaymentsDetails()
    {
        if (!$this->payments) {
            $this->payments = $this->paymentCollectionFactory->create();
            $this->payments = $this->payments->getCollection()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                self::PRIMARY_ID,
                $this->_request->getParam(self::PRIMARY_ID)
            )->getFirstItem();

            return $this->payments->getData();
        }
    }

    /**
     * Get customer id
     */
    public function getCustomerId()
    {
        $customerId = '';
        $paymentsData = $this->paymentCollectionFactory->create()->load($this->_request->getParam(self::PRIMARY_ID));
        if ($paymentsData->getId()) {
            $customerId = $paymentsData->getCustomerId();
        }

        return $customerId;
    }

    /**
     * Get Payment Type title
     *
     * @param string $payment_type
     * @return string
     */
    public function getPaymentTypeTitle($payment_type)
    {
        return $this->scopeConfig->getValue(
            'payment/' . $payment_type . '/title',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
