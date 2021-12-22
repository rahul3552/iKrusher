<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Block\Info;

/**
 * Block for adding target info in Checkmo payment mathod case
 */
class Checkmo extends \Magento\Payment\Block\Info
{

    const PAYMENTMETHOD = 'checkmo';
    const TARGET_CHEQUE_NUMBER = 'target_cheque_number';

    /*
     * @var string
     */

    public $payableTo;

    /**
     * @var string
     */
    public $mailingAddress;

    /**
     *
     * @var string
     */

    protected $_template = 'I95DevConnect_MessageQueue::info/checkmo.phtml';

    /**
     *
     * @var string
     */
    public $chequeNumberFactory;

    /**
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \I95DevConnect\MessageQueue\Model\ChequeNumberFactory $chequeNumber
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \I95DevConnect\MessageQueue\Model\ChequeNumberFactory $chequeNumber
    ) {
        $this->chequeNumberFactory = $chequeNumber;
        parent::__construct($context);
    }

    /**
     * Get Payableto.
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPayableTo()
    {
        if ($this->payableTo === null) {
            $this->_convertAdditionalData();
        }
        return $this->payableTo;
    }

    /**
     * get mailing address
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMailingAddress()
    {
        if ($this->mailingAddress === null) {
            $this->_convertAdditionalData();
        }
        return $this->mailingAddress;
    }

    /**
     * convert additional data
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _convertAdditionalData()
    {

        $this->payableTo = $this->getInfo()->getAdditionalInformation('payable_to');
        $this->mailingAddress = $this->getInfo()->getAdditionalInformation('mailing_address');

        return $this;
    }

    /**
     * To Pdf
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('Magento_OfflinePayments::info/pdf/checkmo.phtml');
        return $this->toHtml();
    }

    /**
     * Retrieve order
     * @return  $order
     */
    public function getOrder()
    {
        return $this->getInfo()->getOrder();
    }

    /**
     * Retrieves target payment
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCheckNumber()
    {
        $checkNumber = '';
        $order = $this->getOrder();
        if ($order) {
            $paymentMethodData = $order->getPayment();
            $paymentMethod = $paymentMethodData->getMethod();
            if ($paymentMethod == self::PAYMENTMETHOD) {
                $sourceId = $order->getId();
                $checkModelData = $this->chequeNumberFactory->create()->getCollection()
                        ->addFieldToSelect(self::TARGET_CHEQUE_NUMBER)
                        ->addFieldToFilter('source_order_id', $sourceId);
                $checkModelData->getSelect()->limit(1);
                $checkModelData = $checkModelData->getData();
                $checkNumber = (isset($checkModelData[0][self::TARGET_CHEQUE_NUMBER]) ?
                                $checkModelData[0][self::TARGET_CHEQUE_NUMBER] : '');
            }
        }
        return $checkNumber;
    }
}
