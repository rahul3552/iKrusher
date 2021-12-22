<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Helper;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use \I95DevConnect\MessageQueue\Api\LoggerInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const C = 'critical';
    const CT = 'creditLimitType';
    const CA = 'creditLimitAmount';
    const A = 'availableLimit';
    const I = 'i95_observer_skip';

    protected $storeManager;
    protected $customer;
    protected $_transportBuilder;
    protected $_inlineTranslation;
    protected $logger;
    public $order;

    /**
     * @var \Magento\Framework\Module\Manager $moduleManager
     */
    protected $moduleManager;

    /**
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    public $customerRepository;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $dataHelper;

    /**
     * @var \I95DevConnect\MessageQueue\Model\SalesOrderFactory
     */
    protected $i95devOrderFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    public $orderFactory;

    /**
     * @var \I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Forward\Customer
     */
    public $customerEntity;

    const XML_PATH_EMAIL_IDENTITY = 'i95dev/i95dev_group/sender_email_identity';
    const COMPLETE_STATUS = 'complete';
    const XML_PATH_EMAIL_SENDER = 'i95devconnect_billpay/billpay_enabled_settings/sender';

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Customer $customer
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param LoggerInterface $logger
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \I95DevConnect\MessageQueue\Model\SalesOrderFactory $i95devOrderFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Forward\Customer $customerEntity
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Customer $customer,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        LoggerInterface $logger,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \I95DevConnect\MessageQueue\Model\SalesOrderFactory $i95devOrderFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Forward\Customer $customerEntity
    ) {
        $this->_scopeConfig = $context->getScopeConfig();
        $this->storeManager = $storeManager;
        $this->customer = $customer;
        $this->_transportBuilder = $transportBuilder;
        $this->_inlineTranslation = $inlineTranslation;
        $this->logger = $logger;
        $this->moduleManager = $moduleManager;
        $this->customerRepository = $customerRepository;
        $this->dataHelper = $dataHelper;
        $this->i95devOrderFactory = $i95devOrderFactory;
        $this->orderFactory = $orderFactory;
        $this->customerEntity = $customerEntity;

        parent::__construct($context);
    }

    public function isBillPayEnabled()
    {
        return $this->_scopeConfig->getValue(
            'i95devconnect_billpay/billpay_enabled_settings/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function sendEmailToCustomer($cashReceipts)
    {
        $customerData = $this->customer->getCollection()
            ->addNameToSelect()
            ->addAttributeToFilter('entity_id', $cashReceipts['CustomerId'])
            ->getFirstItem();

        $customerName = $customerData->getFirstname() . " " . $customerData->getLastname();
        $customerEmail = $customerData->getEmail();

        $template_var = [
            'customername' => $customerName,
            $cashReceipts['ReceiptDocumentNumber'],
            $cashReceipts['AppliedDocumentNumber'],
            $cashReceipts['ModifiedDate'],
            $cashReceipts['PaymentStatus'],
            $cashReceipts['PaymentType'],
            $cashReceipts['ReceiptDocumentAmount'],
            $cashReceipts['PaymentComments'],
            $cashReceipts['CashReceiptNumber']
        ];
        try {
            $this->_inlineTranslation->suspend();
            $transport = $this->_transportBuilder
                    ->setTemplateIdentifier('i95devconnect_billpay_billpay_enabled_settings_email_template')
                    ->setTemplateOptions([
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ])
                    ->setTemplateVars($template_var)
                    ->setFrom($this->_scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER))
                    ->addTo($customerEmail)
                    ->getTransport();

            $transport->sendMessage();
            $this->_inlineTranslation->resume();
            return true;
        } catch (\Exception $e) {
            $this->logger->createLog(
                __METHOD__,
                $e->getMessage(),
                LoggerInterface::I95EXC,
                self::C
            );
        }
    }

    /**
     * Update customer credit limit
     *
     * @createdBy Debashis S. Gopal
     * @param array $customerData
     * @return boolean
     */
    public function updateCustomerCreditlimit($customerData)
    {
        try {
            if ($this->creditlimitEnabled()) {
                $customerId = $customerData['sourceId'];
                $customerD = $this->customerRepository->getById($customerId);
                if (isset($customerData[self::CT])
                    && !empty($customerData[self::CT])
                    ) {
                    $customerD->setCustomAttribute('credit_limit_type', $customerData[self::CT]);
                }

                if (isset($customerData[self::CA])
                    && !empty($customerData[self::CA])
                    ) {
                    $customerD->setCustomAttribute('credit_limit_amount', $customerData[self::CA]);
                }

                if (isset($customerData[self::AL])
                    && !empty($customerData[self::AL])
                ) {
                    $customerD->setCustomAttribute('available_limit', $customerData[self::AL]);
                }

                $this->dataHelper->unsetGlobalValue(self::I);
                $this->dataHelper->setGlobalValue(self::I, true);

                $this->customerRepository->save($customerD);
                $this->dataHelper->unsetGlobalValue(self::I);
            } else {
                $this->logger->createLog(
                    __METHOD__,
                    "creditlimit is not Enabled",
                    LoggerInterface::I95EXC,
                    self::C
                );
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(
                __METHOD__,
                $ex->getMessage(),
                LoggerInterface::I95EXC,
                self::C
            );
        }
        return true;
    }

    /**
     * Check if I95DevConnect_Creditlimits module enabled and creditlimits payment method active.
     *
     * @return string|null
     */
    public function creditlimitEnabled()
    {
        if ($this->moduleManager->isEnabled("I95DevConnect_Creditlimits")) {
            return $this->_scopeConfig->getValue(
                'payment/creditlimits/active',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        return false;
    }

    /**
     * @param $targetOrderId
     * @return mixed
     * @addedBy Subhan. targetcustomerId is being retrieved from targetOrderId of input string
     */
    public function getCustomerFromOrder($targetOrderId)
    {
        $orderData = $this->i95devOrderFactory->create()->load($targetOrderId, 'target_order_id');
        $this->order = $this->orderFactory->create()->loadByIncrementId($orderData->getSourceOrderId());
        return $this->customerEntity->getCustomerEntity($this->order);
    }
}
