<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Controller\Adminhtml\Index;

/**
 * Emails related to BillPay
 */
class Email extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;
    protected $arPaymentModel;

    /**
     * @var \I95DevConnect\BillPay\Model\Customer\Payment\Email\Sender
     */
    protected $emailSender;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \I95DevConnect\BillPay\Model\ArPayment $arPaymentModel
     * @param \I95DevConnect\BillPay\Model\Customer\Payment\Email\Sender
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \I95DevConnect\BillPay\Model\ArPayment $arPaymentModel,
        \I95DevConnect\BillPay\Model\Customer\Payment\Email\SenderFactory $emailSender
    ) {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->arPaymentModel = $arPaymentModel;
        $this->emailSender = $emailSender;
    }

    /**
     * Notify user
     *
     * @return \Magento\Backend\Model\View\Result\Forward|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $paymentId = $this->getRequest()->getParam('payment_id');

        if (!$paymentId) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $collection = $this->arPaymentModel->load($paymentId);
        $customerId = $collection->getData('customer_id');

        $mailStatus = $this->emailSender->create()->send($paymentId, $customerId);

        if ($mailStatus) {
            $this->messageManager->addSuccess(__('Email Sent'));
        } else {
            $this->messageManager->addSuccess(
                __('Some error occure while sending email. Please contact Administrator.')
            );
        }

        return $this->resultRedirectFactory->create()->setPath(
            'billpay/index/paymentview',
            ['primary_id' => $paymentId]
        );
    }
}
