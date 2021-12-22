<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 * @codingStandardsIgnoreFile
 */

namespace I95DevConnect\BillPay\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class PayCharges extends \Magento\Framework\App\Action\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $paymentObject;
    protected $payment;
    protected $paymentInterface;
    protected $paymentFactory;
    protected $salesOrder;
    protected $paymentData;
    protected $customerFactory;
    protected $addressInterface;
    protected $resultRedirect;
    protected $messageManager;
    protected $arPayment;
    protected $arBook;
    protected $arPaymentDetails;
    protected $logger;
    protected $billpayHelper;
    protected $cardFactory;
    public $eventManager;
    public $date;
    protected $pricingHelper;

    /**
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Sales\Api\Data\OrderPaymentInterface $paymentObject
     * @param \Magento\Quote\Model\Quote\Payment $payment
     * @param \Magento\Payment\Api\PaymentMethodListInterface $paymentInterface
     * @param \Magento\Payment\Model\Method\InstanceFactory $paymentFactory
     * @param \Magento\Sales\Model\Order $salesOrder
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Sales\Api\Data\OrderAddressInterfaceFactory $addressInterface
     * @param \I95DevConnect\BillPay\Model\ArPaymentFactory $arPayment
     * @param \I95DevConnect\BillPay\Model\ArbookFactory $arBook
     * @param \I95DevConnect\BillPay\Model\ArPaymentDetailsFactory $arPaymentDetails
     * @param \Psr\Log\LoggerInterface $logger
     * @param \I95DevConnect\BillPay\Helper\Data $billpayHelper
     * @param \ParadoxLabs\TokenBase\Model\CardFactory $cardFactory
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     */
    public function __construct( // NOSONAR
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Sales\Api\Data\OrderPaymentInterface $paymentObject,
        \Magento\Quote\Model\Quote\Payment $payment,
        \Magento\Payment\Api\PaymentMethodListInterface $paymentInterface,
        \Magento\Payment\Model\Method\InstanceFactory $paymentFactory,
        \Magento\Sales\Model\Order $salesOrder,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Sales\Api\Data\OrderAddressInterfaceFactory $addressInterface,
        \I95DevConnect\BillPay\Model\ArPaymentFactory $arPayment,
        \I95DevConnect\BillPay\Model\ArbookFactory $arBook,
        \I95DevConnect\BillPay\Model\ArPaymentDetailsFactory $arPaymentDetails,
        \Psr\Log\LoggerInterface $logger,
        \I95DevConnect\BillPay\Helper\Data $billpayHelper,
        \ParadoxLabs\TokenBase\Model\CardFactory $cardFactory,
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->paymentObject = $paymentObject;
        $this->payment = $payment;
        $this->paymentInterface = $paymentInterface;
        $this->paymentFactory = $paymentFactory;
        $this->salesOrder = $salesOrder;
        $this->paymentData = $paymentData;
        $this->customerFactory = $customerFactory;
        $this->addressInterface = $addressInterface;
        $this->resultRedirect = $context->getResultFactory();
        $this->arPayment = $arPayment;
        $this->arBook = $arBook;
        $this->arPaymentDetails = $arPaymentDetails;
        $this->logger = $logger;
        $this->billpayHelper = $billpayHelper;
        $this->cardFactory = $cardFactory;
        $this->eventManager = $eventManager;
        $this->date = $date;
        $this->pricingHelper = $pricingHelper;
        parent::__construct($context);
    }

    /**
     * Receipt details class
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute() // NOSONAR
    {
        $data = $this->getRequest()->getPostValue();
        $paymentId = '';
        try {
            $paymentMethod = $data['payment']['method'];
            $customerID = $data['customer_id'];
            $paymentStatus = '';
            if ($paymentMethod == "checkmo" && !$this->checkTransactionNumber($data['payment']['check_number'], $customerID)) {
                $this->messageManager->addError("Please enter valid check number");
                return $this->_redirect('billpay/index/managepayment', ['_current' => true]); // NOSONAR
            }
            $custObj = $this->customerFactory->create()->load($data['customer_id']);
            if ($data['payment']['method'] == 'authnetcim' || $data['payment']['method'] == 'payflowpro'
            || $data['payment']['method'] == 'chargelogic_connect') {
                $amountToCapture = $this->getAmountToCapture($data);
                $payment = $this->paymentObject; // NOSONAR
                $payment->setMethod($data['payment']['method']);
                $payment->setData("amount_ordered", $amountToCapture);
                $payment->setData("base_amount_ordered", $amountToCapture);
                $payment->setData("shipping_amount", 0);
                $payment->setData("base_shipping_amount", 0);
                $payment->setData("amount_authorized", $amountToCapture);
                $payment->setData("base_amount_authorized", $amountToCapture);
                $card_id = (isset($data['payment']['card_id'])) ? $data['payment']['card_id'] : '';
                if ($card_id != '') {
                    $card = $this->cardFactory->create()->load($data['payment']['card_id'], "hash");
                    if ($card->getId()) {
                        $payment->setData("tokenbase_id", $card->getId());
                    }
                } else {
                    $ccType = (isset($data['payment']['cc_type']) ? $data['payment']['cc_type'] : "");
                    $method = (isset($data['payment']['method']) ? $data['payment']['method'] : "");
                    $ccNumber = (isset($data['payment']['cc_number']) ? $data['payment']['cc_number'] : "");
                    $ccExpMonth = (isset($data['payment']['cc_exp_month']) ? $data['payment']['cc_exp_month'] : "");
                    $ccExpYear = (isset($data['payment']['cc_exp_year']) ? trim($data['payment']['cc_exp_year']) : "");
                    $ccCid = (isset($data['payment']['cc_cid']) ? $data['payment']['cc_cid'] : "");
                    $payment->setData("tokenbase_id", null);
                    $payment->setCcType($ccType);
                    $payment->setData("cc_last_4", substr($ccNumber, -4));
                    $payment->setData("cc_exp_month", $ccExpMonth);
                    $payment->setData("cc_exp_year", $ccExpYear);
                    $payment->setData("cc_number", $ccNumber);
                    $payment->setData("cc_cid", $ccCid);
                }
                $order = $this->salesOrder;

                if (!is_bool($custObj->getDefaultBillingAddress()) && !is_bool($custObj->getDefaultShippingAddress())) {
                    $billingAddress = $custObj->getDefaultBillingAddress();
                    $shippingAddress = $custObj->getDefaultShippingAddress();
                    $address = $this->addressInterface->create();
                    $address->setFirstname($shippingAddress->getFirstname());
                    $address->setLastname($shippingAddress->getLastname());
                    $address->setCity($shippingAddress->getCity());
                    $address->setRegionId($shippingAddress->getRegionId());
                    $address->setCountryId($shippingAddress->getCountryId());
                    $address->setPostcode($shippingAddress->getPostcode());
                    $address->setTelephone($shippingAddress->getTelephone());
                    $address->setFax($shippingAddress->getFax());
                    $address->setCustomerId($data['customer_id']);
                    $address->setStreet($shippingAddress->getStreet());
                    $address->setCompany($shippingAddress->getCompany());
                    $order->setShippingAddress($address);
                    $billing_address = $this->addressInterface->create();
                    $billing_address->setFirstname($billingAddress->getFirstname());
                    $billing_address->setLastname($billingAddress->getLastname());
                    $billing_address->setCity($billingAddress->getCity());
                    $billing_address->setRegionId($billingAddress->getRegionId());
                    $billing_address->setCountryId($billingAddress->getCountryId());
                    $billing_address->setPostcode($billingAddress->getPostcode());
                    $billing_address->setTelephone($billingAddress->getTelephone());
                    $billing_address->setFax($billingAddress->getFax());
                    $billing_address->setCustomerId($data['customer_id']);
                    $billing_address->setStreet($billingAddress->getStreet());
                    $billing_address->setCompany($billingAddress->getCompany());
                    $order->setBillingAddress($billing_address);
                    $order->setCustomerEmail($custObj->getEmail());
                    $order->setCustomerId($data['customer_id']);
                    $payment->setOrder($order);

                    foreach ($this->getPaymentMethods($data['payment']['method']) as $method) {
                        $method->setInfoInstance($this->payment);
                        $instance = $this->paymentData->getMethodInstance($data['payment']['method']);
                        $instance->setInfoInstance($this->payment);
                        $payment->setAdditionalInformation(["method_title" => $method->getTitle()]);
                        $method->setStore(
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        );

                        $method->capture($payment, $amountToCapture);
                    }

                    $transactionId = $payment->getTransactionId();
                    $isSuccess['flag'] = 'Success';
                } else {
                    $this->messageManager->addError(
                        __('Customer should have default addresses in order to paywith this method.')
                    );
                    return $this->_redirect('customer/account', ['_current' => true]);
                }
            }
            if ($paymentMethod == 'checkmo' || $paymentMethod == 'cashondelivery' || $isSuccess['flag'] == 'Success') {
                if ($paymentMethod == 'checkmo') {
                    $paymentTransactionId = isset($data['payment']['check_number']) ? $data['payment']['check_number'] : '';
                }

                if ($paymentMethod == 'authnetcim' || $paymentMethod == 'payflowpro' ||  $paymentMethod == 'chargelogic_connect' ) {
                    $paymentTransactionId = (!empty($transactionId) && $transactionId != "") ? $transactionId : 0;
                }

                if ($paymentMethod == 'cashondelivery') {
                    $paymentTransactionId = 'COD' . rand(100, 10000);
                }

                $invoiceParams = $data['invoices'];
                foreach ($invoiceParams as $eachInvoiceParams) {
                    $invoiceData = $this->arBook->create()->load($eachInvoiceParams['each_entity_id']);
                    if (isset($data['check_invoice'])
                        && $eachInvoiceParams['check_invoice'] == $data['check_invoice']
                    ) {
                        $invoiceData->setOrderStatus('processing');
                        $paymenDate = $this->date->gmtDate();
                        $arPayments = $this->arPayment->create();
                        $arPayments->setPaymentType($paymentMethod);
                        $arPayments->setPaymentTransId($paymentTransactionId);
                        $arPayments->setStatus('processing');
                        $arPayments->setTargetSyncStatus(0);
                        $arPayments->setCustomerId($customerID);
                        $currentDate = date('Y-m-d');
                        $docDiscountAmount = $invoiceData->getDiscountAmount();
                        $docDiscountDate = date('Y-m-d', strtotime($invoiceData->getDiscountDt()));
                        $docAmountToCapture = $invoiceData->getOutstandingAmount();
                        if (strtotime($docDiscountDate) >= strtotime($currentDate)) {
                            $docAmountToCapture -= $docDiscountAmount;
                        }

                        $arPayments->setTotalAmt($docAmountToCapture);
                        $arPayments->setPaymentDate($paymenDate);
                        $arPayments->setPaymentComment($data['payment']['payment_comment']);
                        $arPayments->save();
                        $paymentId = $arPayments->getData('primary_id');
                        $paymentStatus = $arPayments->getData('status');
                        $arPaymentsDetails = $this->arPaymentDetails->create();
                        $arPaymentsDetails->setTargetInvoiceId($eachInvoiceParams['each_invoice_id']);
                        $arPaymentsDetails->setPaymentId($arPayments->getPrimaryId());
                        $arPaymentsDetails->setStatus('paid');
                        $arPaymentsDetails->setAmount(
                            isset($eachInvoiceParams['each_paid_amount']) ? $eachInvoiceParams['each_paid_amount'] : ""
                        );
                        $arPaymentsDetails->setArId($eachInvoiceParams['each_entity_id']);
                        $arPaymentsDetails->save();
                    }
                    $invoiceData->save();
                }
                $this->messageManager->addSuccess(__('Payment Done Successfully.'));

                $mailParams = [
                    'CustomerId' => $custObj->getId(),
                    'ReceiptDocumentNumber' => $paymentId,
                    'PaymentType' => $paymentMethod,
                    'AppliedDocumentNumber' => $paymentTransactionId,
                    'PaymentStatus' => $paymentStatus,
                    'ModifiedDate' => date("n/j/Y h:i:s A", strtotime($paymenDate)),
                    'PaymentComments' => $data['payment']['payment_comment'],
                    'ReceiptDocumentAmount' => $this->formatPrice(number_format((float)$this->getAmountToCapture($data), 2, '.', '')),
                    'CashReceiptNumber' => ''
                ];

                $eventname = 'erpconnect_billpay_accountreceivables_aftersave';
                $this->eventManager->dispatch($eventname, ['data_object' => $arPayments]);

                //Send Email to Customer and Bcc to Owner
                $this->billpayHelper->sendEmailToCustomer($mailParams);

                $this->_redirect(
                    'billpay/index/paymentdetails',
                    ['_current' => true, 'payment_id' => $paymentId]
                );
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            if ($paymentId == '') {
                return $this->_redirect('billpay/index/managepayment', ['_current' => true]);
            } else {
                return $this->_redirect(
                    'billpay/index/paymentdetails',
                    ['_current' => true, 'payment_id' => $paymentId]
                );
            }
        }
    }

    /**
     * Retrieve available payment methods
     *
     * @return array
     */
    public function getPaymentMethods($methodCode)
    {
        $store = null;
        $methods = [];

        foreach ($this->paymentInterface->getActiveList($store) as $method) {
            $methodInstance = $this->paymentFactory->create($method);
            if ($methodCode == $methodInstance->getCode()) {
                $methods[] = $methodInstance;
            }
        }

        return $methods;
    }

    /**
     * validating check number
     *
     * @return boolean
     */
    public function checkTransactionNumber($checkNumber, $customerId)
    {
        $arPayments = $this->arPayment->create()->getCollection()
            ->addFieldToFilter("payment_type", "checkmo")
            ->addFieldToFilter("customer_id", $customerId);
        $records = $arPayments->getData();
        if (!empty($records)) {
            foreach ($records as $individualRecord) {
                if ($individualRecord['payment_trans_id'] == $checkNumber) {
                    return false;
                }
            }
        } else {
            return true;
        }
        return true;
    }

    public function formatPrice($price)
    {
        return $this->pricingHelper->currency($price, true, false);
    }

    public function getAmountToCapture($data)
    {
        $amountToCapture = 0;
        $invoiceList = $data['invoices'];
        $currentDate = date('Y-m-d');
        foreach ($invoiceList as $eachInvoice) {
            $invoiceData = $this->arBook->create()->load($eachInvoice['each_entity_id']);
            if (isset($data['check_invoice']) && $eachInvoice['check_invoice'] == $data['check_invoice']) {
                $amountToCapture += $invoiceData->getOutstandingAmount();
                $discountAmount = $invoiceData->getDiscountAmount();
                $discountDate = date('Y-m-d', strtotime($invoiceData->getDiscountDt()));
                if (strtotime($discountDate) >= strtotime($currentDate)) {
                    $amountToCapture -= $discountAmount;
                }
            }
        }

        return $amountToCapture;
    }
}
