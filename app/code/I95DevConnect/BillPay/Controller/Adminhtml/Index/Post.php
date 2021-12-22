<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 * @codingStandardsIgnoreFile
 */

namespace I95DevConnect\BillPay\Controller\Adminhtml\Index;

use I95DevConnect\BillPay\Model\ArbookFactory;
use I95DevConnect\BillPay\Model\ArPaymentDetailsFactory;
use I95DevConnect\BillPay\Model\ArPaymentFactory;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Event\Manager;
use Magento\Payment\Api\PaymentMethodListInterface;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\Method\InstanceFactory;
use Magento\Quote\Model\Quote\Payment;
use Magento\Sales\Api\Data\OrderAddressInterfaceFactory;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;
use ParadoxLabs\TokenBase\Model\CardFactory;

class Post extends \Magento\Backend\App\Action
{

    const CARDID = 'card_id';
    const PAYMENT = 'payment';
    const METHOD = 'method';
    const CHECKMO = 'checkmo';
    const CHECKNUMBER = 'check_number';
    const CURRENT = '_current';
    const CUSTOMERID = 'customer_id';
    const CCNUMBER = 'cc_number';
    const CCEXPY = 'cc_exp_year';
    const CCEXPM = 'cc_exp_month';
    const CCCID = 'cc_cid';
    const CHECK_INVOICE = 'check_invoice';
    const PAYNOW = 'pay_now';
    const PROCESSING = 'processing';
    const AMOUNT_CAPTURE = 'amount_capture';
    const CUSTOMER_INDEX_EDIT = 'customer/index/edit';

    protected $paymentObject;
    protected $payment;
    protected $paymentInterface;
    protected $paymentFactory;
    protected $salesOrder;
    protected $paymentData;
    protected $customerFactory;
    protected $addressInterface;
    protected $arPayment;
    protected $arBook;
    protected $arPaymentDetails;
    protected $billpayHelper;
    protected $cardFactory;
    public $eventManager;
    protected $pricingHelper;
    public $isSuccess;

    /**
     *
     * @param Context $context
     * @param OrderPaymentInterface $paymentObject
     * @param Payment $payment
     * @param PaymentMethodListInterface $paymentInterface
     * @param InstanceFactory $paymentFactory
     * @param Order $salesOrder
     * @param Data $paymentData
     * @param CustomerFactory $customerFactory
     * @param OrderAddressInterfaceFactory $addressInterface
     * @param ArPaymentFactory $arPayment
     * @param ArbookFactory $arBook
     * @param ArPaymentDetailsFactory $arPaymentDetails
     * @param \I95DevConnect\BillPay\Helper\Data $billpayHelper
     * @param CardFactory $cardFactory
     * @param Manager $eventManager
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     */
    public function __construct(
        Context $context,
        OrderPaymentInterface $paymentObject,
        Payment $payment,
        PaymentMethodListInterface $paymentInterface,
        InstanceFactory $paymentFactory,
        Order $salesOrder,
        Data $paymentData,
        CustomerFactory $customerFactory,
        OrderAddressInterfaceFactory $addressInterface,
        ArPaymentFactory $arPayment,
        ArbookFactory $arBook,
        ArPaymentDetailsFactory $arPaymentDetails,
        \I95DevConnect\BillPay\Helper\Data $billpayHelper,
        CardFactory $cardFactory,
        Manager $eventManager,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper
    ) {
        $this->paymentObject = $paymentObject;
        $this->payment = $payment;
        $this->paymentInterface = $paymentInterface;
        $this->paymentFactory = $paymentFactory;
        $this->salesOrder = $salesOrder;
        $this->paymentData = $paymentData;
        $this->customerFactory = $customerFactory;
        $this->addressInterface = $addressInterface;
        $this->arPayment = $arPayment;
        $this->arBook = $arBook;
        $this->arPaymentDetails = $arPaymentDetails;
        $this->billpayHelper = $billpayHelper;
        $this->cardFactory = $cardFactory;
        $this->eventManager = $eventManager;
        $this->pricingHelper = $pricingHelper;

        parent::__construct($context);
    }

    /**
     * return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $paymentId = '';
        try {
            $paymentMethod = $data[self::PAYMENT][self::METHOD];
            $customerID = $data[self::CUSTOMERID];
            if ($paymentMethod == self::CHECKMO && (!$this->checkTransactionNumber($data[self::PAYMENT][self::CHECKNUMBER], $customerID))) {
                $this->messageManager->addError("this check number has been entered for this customer already");
                return $this->_redirect(self::CUSTOMER_INDEX_EDIT, [self::CURRENT => true, 'id' => $data[self::CUSTOMERID]]);
            }
            $custObj = $this->customerFactory->create()->load($data[self::CUSTOMERID]);
            $this->setAuthPaymentData($data,$custObj);

            if ($paymentMethod == self::CHECKMO || $paymentMethod == 'cashondelivery' || $this->isSuccess == 'Success') {

                $paymentTransactionId = $this->checkNumber($paymentMethod,$data,$customerID);

                $currentDate = date('Y-m-d H:i:s');
                $paymenDate = $currentDate;
                $arPayments = $this->arPayment->create();
                $arPayments->setPaymentType($paymentMethod);
                $arPayments->setPaymentTransId($paymentTransactionId);
                $arPayments->setStatus(self::PROCESSING);
                $arPayments->setPaymentComment($data[self::PAYMENT]['payment_comment']);
                $arPayments->setCustomerId($customerID);
                $arPayments->setTargetSyncStatus(0);
                $arPayments->setTotalAmt($data[self::AMOUNT_CAPTURE]);
                $arPayments->setPaymentDate($paymenDate);
                $arPayments->save();
                $paymentId = $arPayments->getData('primary_id');
                $paymentStatus = $arPayments->getData('status');
                $invoiceParams = $data['invoices'];
                foreach ($invoiceParams as $eachInvoiceParams) {
                    $this->invoiceProcess($eachInvoiceParams,$paymentId);
                }

                $this->messageManager->addSuccess(__('Payment Done Successfully.'));

                $mailParams = [
                    'CustomerId' => $custObj->getId(),
                    'ReceiptDocumentNumber' => $paymentId,
                    'PaymentType' => $paymentMethod,
                    'AppliedDocumentNumber' => $paymentTransactionId,
                    'PaymentStatus' => $paymentStatus,
                    'ModifiedDate' => $paymenDate,
                    'PaymentComments' => $data[self::PAYMENT]['payment_comment'],
                    'ReceiptDocumentAmount' => $this->formatPrice($data[self::AMOUNT_CAPTURE]),
                    'CashReceiptNumber' => ''
                ];

                $eventname = 'erpconnect_billpay_accountreceivables_aftersave';
                $this->eventManager->dispatch($eventname, ['data_object' => $arPayments]);

                //Send Email to Customer and Bcc to Owner
                $this->billpayHelper->sendEmailToCustomer($mailParams);

                return $this->_redirect('billpay/index/paymentview', [self::CURRENT => true, 'primary_id' => $paymentId]);
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $path = self::CUSTOMER_INDEX_EDIT;
            $data = [self::CURRENT => true, 'id' => $data[self::CUSTOMERID]];
            if ($paymentId == '') {
                $data = [self::CURRENT => true, 'payment_id' => $paymentId];
                $path = 'billpay/index/paymentview';
            }
            return $this->_redirect($path, $data);

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
        ->addFieldToFilter("payment_type", self::CHECKMO)
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

    /**
     * @param $number
     * @return string
     * @addedBy Subhan
     */
    public function getCardType($number)
    {
        $return = 'VI';
        $cardtype = array(
            "visa"       => "/^4[0-9]{12}(?:[0-9]{3})?$/",
            "mastercard" => "/^5[1-5][0-9]{14}$/",
            "amex"       => "/^3[47][0-9]{13}$/",
            "discover"   => "/^6(?:011|5[0-9]{2})[0-9]{12}$/",
        );

        if (preg_match($cardtype['visa'],$number))
        {
            $return = 'VI';

        }
        else if (preg_match($cardtype['mastercard'],$number))
        {
            $return = 'MC';
        }
        else if (preg_match($cardtype['amex'],$number))
        {
            $return = 'AE';

        }
        else if (preg_match($cardtype['discover'],$number))
        {
            $return = 'DI';
        }

        return $return;
    }

    public function setPaymentData($data,&$ccType,&$method,&$ccNumber,&$ccExpMonth,&$ccExpYear,&$ccCid)
    {
        $ccType = (isset($data[self::PAYMENT]['cc_type']) ? $data[self::PAYMENT]['cc_type'] : "");
        if($ccType == '') {
            $ccType = $this->getCardType($data[self::PAYMENT][self::CCNUMBER]);
        }
        $method = (isset($data[self::PAYMENT][self::METHOD]) ? $data[self::PAYMENT][self::METHOD] : "");
        $ccNumber = (isset($data[self::PAYMENT][self::CCNUMBER]) ? $data[self::PAYMENT][self::CCNUMBER] : "");
        $ccExpMonth = (isset($data[self::PAYMENT][self::CCEXPM]) ? $data[self::PAYMENT][self::CCEXPM] : "");
        $ccExpYear = (isset($data[self::PAYMENT][self::CCEXPY]) ? $data[self::PAYMENT][self::CCEXPY] : "");
        $ccCid = (isset($data[self::PAYMENT][self::CCCID]) ? $data[self::PAYMENT][self::CCCID] : "");
    }

    public function createAddress(\Magento\Customer\Model\Address $address,$customerId)
    {
        $newAddress = $this->addressInterface->create();
        $newAddress->setFirstname($address->getFirstname());
        $newAddress->setLastname($address->getLastname());
        $newAddress->setCity($address->getCity());
        $newAddress->setRegionId($address->getRegionId());
        $newAddress->setCountryId($address->getCountryId());
        $newAddress->setPostcode($address->getPostcode());
        $newAddress->setTelephone($address->getTelephone());
        $newAddress->setFax($address->getFax());
        $newAddress->setCustomerId($customerId);
        $newAddress->setStreet($address->getStreet());
        $newAddress->setCompany($address->getCompany());
        return $newAddress;
    }

    public function checkNumber($paymentMethod,$data,$customerID)
    {
        if ($paymentMethod == self::CHECKMO && (!$this->checkTransactionNumber($data[self::PAYMENT][self::CHECKNUMBER], $customerID))) {
            $this->messageManager->addError("Please enter valid check number");
            return $this->_redirect('billpay/index/managepayment', [self::CURRENT => true]);
        }
    }

    public function invoiceProcess($eachInvoiceParams,&$paymentId)
    {
        $invoiceData = $this->arBook->create()->load($eachInvoiceParams[self::CHECK_INVOICE]);
        if ($eachInvoiceParams[self::PAYNOW] != 0.00 && $eachInvoiceParams[self::PAYNOW] != null) {
            $payable = $eachInvoiceParams['outstanding_amount'] - $eachInvoiceParams['discount_amount'];
            if($payable !== $eachInvoiceParams[self::PAYNOW]) {
                $balance = $eachInvoiceParams['outstanding_amount'] - $eachInvoiceParams[self::PAYNOW];
                $invoiceData->setOutstandingAmount($balance);
                if ($balance == $eachInvoiceParams['discount_amount']) {
                    $invoiceData->setOutstandingAmount(0.00);
                    $invoiceData->setOrderStatus(self::PROCESSING);
                }
            }
            else {
                $invoiceData->setOrderStatus(self::PROCESSING);
            }

            $invoiceData->setModifiedBy("Magento");

            $arPaymentsDetails = $this->arPaymentDetails->create();
            $arPaymentsDetails->setTargetInvoiceId($eachInvoiceParams['target_invoice_id']);
            $arPaymentsDetails->setPaymentId($paymentId);
            $arPaymentsDetails->setStatus('paid');
            $arPaymentsDetails->setAmount(
                isset($eachInvoiceParams[self::PAYNOW]) ? $eachInvoiceParams[self::PAYNOW] : ""
            );
            $arPaymentsDetails->setArId($eachInvoiceParams[self::CHECK_INVOICE]);
            $arPaymentsDetails->save();
        }

        $invoiceData->save();

    }

    public function setAuthPaymentData($data,$custObj)
    {
        if ($data[self::PAYMENT][self::METHOD] == 'authnetcim' || $data[self::PAYMENT][self::METHOD] == 'payflowpro' || $data[self::PAYMENT][self::METHOD] == 'chargelogic_connect') {
            $paymentO = $this->paymentObject;
            $amountToCapture = $data[self::AMOUNT_CAPTURE];
            $paymentO->setData("amount_ordered", $amountToCapture);
            $paymentO->setData("base_amount_ordered", $amountToCapture);
            $paymentO->setData("shipping_amount", 0);
            $paymentO->setData("base_shipping_amount", 0);
            $paymentO->setData("amount_authorized", $amountToCapture);
            $paymentO->setData("base_amount_authorized", $amountToCapture);
            $card_id = $data[self::PAYMENT][self::CARDID] ?? null;
            if ($card_id) {
                $card = $this->cardFactory->create()->load($data[self::PAYMENT][self::CARDID], "hash");
                if ($card->getId()) {
                    $paymentO->setData("tokenbase_id", $card->getId());
                }
            } else {
                $ccType = '';
                $method= '';
                $ccNumber= '';
                $ccExpMonth= '';
                $ccExpYear= '';
                $ccCid= '';
                $this->setPaymentData($data,$ccType,$method,$ccNumber,$ccExpMonth,$ccExpYear,$ccCid);
                $paymentO->setData("tokenbase_id", null);
                $paymentO->setMethod($method);
                $paymentO->setCcType($ccType);
                $paymentO->setData("cc_last_4", substr($ccNumber, -4));
                $paymentO->setData(self::CCEXPM, $ccExpMonth);
                $paymentO->setData(self::CCEXPY, $ccExpYear);
                $paymentO->setData("cc_number", $ccNumber);
                $paymentO->setData(self::CCCID, $ccCid);
            }

            $order = $this->salesOrder;
            if (!is_bool($custObj->getDefaultBillingAddress()) && !is_bool($custObj->getDefaultShippingAddress())) {
                $billingAddress = $custObj->getDefaultBillingAddress();
                $shippingAddress = $custObj->getDefaultShippingAddress();
                $address = $this->createAddress($shippingAddress,$data[self::CUSTOMERID]);
                $order->setShippingAddress($address);
                $billing_address = $this->createAddress($billingAddress,$data[self::CUSTOMERID]);
                $order->setBillingAddress($billing_address);
                $order->setCustomerEmail($custObj->getEmail());
                $order->setCustomerId($data[self::CUSTOMERID]);
                $paymentO->setOrder($order);
                foreach ($this->getPaymentMethods($data[self::PAYMENT][self::METHOD]) as $method) {
                    $method->setInfoInstance($this->payment);
                    $instance = $this->paymentData->getMethodInstance($data[self::PAYMENT][self::METHOD]);
                    $instance->setInfoInstance($this->payment);
                    $paymentO->setAdditionalInformation(["method_title" => $method->getTitle()]);
                    $method->setStore(
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    );

                    $method->capture($paymentO, $amountToCapture);
                }
                $this->isSuccess= 'Success';
            } else {
                $this->messageManager->addError(
                    __('Customer should have default addresses in order to paywith this method.')
                );
                return $this->_redirect(self::CUSTOMER_INDEX_EDIT, [self::CURRENT => true, 'id' => $data[self::CUSTOMERID]]);
            }
        }

    }

}
