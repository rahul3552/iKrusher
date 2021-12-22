<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Model\Customer\Payment\Email;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;

/**
 * Class InvoiceSender
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Sender extends \Magento\Sales\Model\Order\Email\Sender
{

    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @var InvoiceResource
     */
    protected $invoiceResource;
    protected $_pdfModel;
    protected $_customerModel;
    protected $_scopeConfig;
    protected $_transportBuilder;
    protected $_inlineTranslation;

    const XML_PATH_EMAIL_RECIPIENT = 'i95devconnect_billpay/billpay_enabled_settings/recipient_email';
    const XML_PATH_EMAIL_SENDER = 'i95devconnect_billpay/billpay_enabled_settings/sender';
    const XML_PATH_EMAIL_TEMPLATE = 'i95devconnect_billpay/billpay_enabled_settings/email_template';

    /**
     * @param \I95DevConnect\BillPay\Model\Customer\Payment\Pdf\View $pdfModel
     * @param \Magento\Customer\Model\Customer $customerModel
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        \I95DevConnect\BillPay\Model\Customer\Payment\Pdf\View $pdfModel,
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation
    ) {
        $this->_pdfModel = $pdfModel;
        $this->_customerModel = $customerModel;
        $this->_scopeConfig = $scopeConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->_inlineTranslation = $inlineTranslation;
    }

    /**
     * Sends payment detail email to the customer.
     *
     * @param int $paymentId
     * @param int $customerId
     * @return bool
     */
    public function send($paymentId, $customerId)
    {
        if (isset($paymentId)) {
            $ardetails = $this->_pdfModel->getArdetail($paymentId);
        }
        if (isset($customerId)) {
            $customerData = $this->_customerModel->load($customerId);
            $name = $customerData['firstname'];
            $email = $customerData['email'];
            $ardetails['customername'] = $name;
        }

        try {
            $this->_inlineTranslation->suspend();
            $transport = $this->_transportBuilder
                    ->setTemplateIdentifier($this->_scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE))
                    ->setTemplateOptions([
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ])
                    ->setTemplateVars($ardetails)
                    ->setFrom($this->_scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER))
                    ->addTo($email)
                    ->getTransport();

            $transport->sendMessage();
            $this->_inlineTranslation->resume();
            return true;
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\MailException(__('Error in sending email'));
        }
    }
}
