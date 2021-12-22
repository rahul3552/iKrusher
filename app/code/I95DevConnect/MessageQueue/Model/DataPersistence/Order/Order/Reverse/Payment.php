<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 * @updatedBy Divya Koona. Removed addPaymentInfo() method as it is not used anywhere
 */
namespace I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Reverse;

use \I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\AbstractOrder;

/**
 * Class Add Payment information while creating an order
 */
class Payment extends AbstractOrder
{

    const I95EXC = 'i95devApiException';
    const PAYMENTMETHOD = "paymentMethod";
    public $paymentData;

    /**
     * @var Config
     */
    public $paymentModelConfig;

    /**
     * @var ScopeConfigInterface
     */
    public $appConfigScopeConfigInterface;

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $dataHelper;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \Magento\Payment\Model\Config $paymentModelConfig
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $appConfigScopeConfigInterface
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param \I95DevConnect\MessageQueue\Helper\Generic $genericHelper
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \Magento\Payment\Model\Config $paymentModelConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $appConfigScopeConfigInterface,
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        \I95DevConnect\MessageQueue\Helper\Generic $genericHelper,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
    ) {
        $this->dataHelper = $dataHelper;
        $this->paymentModelConfig = $paymentModelConfig;
        $this->appConfigScopeConfigInterface = $appConfigScopeConfigInterface;
        parent::__construct(
            $logger,
            $genericHelper,
            $validate
        );
    }

    /**
     * Validate request payment data
     *
     * @param array $stringData
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     * @author Divya Koona
     */
    public function validateData($stringData)
    {
        $this->stringData = $stringData;
        $this->paymentData = $this->dataHelper->getValueFromArray("payment", $this->stringData);
        $activeMethods = $this->getActivePaymentMethods();
        foreach ($this->paymentData as $payment) {
            if (!in_array($this->dataHelper->getValueFromArray(self::PAYMENTMETHOD, $payment), $activeMethods)) {
                throw new \Magento\Framework\Exception\LocalizedException(__("i95dev_order_022"));
            }
        }
        return true;
    }

    /**
     * Get Active Payment Methods
     *
     * @return array $methods
     * @throws \Magento\Framework\Exception\LocalizedException
     * @author Divya Koona
     */
    public function getActivePaymentMethods()
    {
        $methods = [];
        try {
            $payments = $this->paymentModelConfig->getActiveMethods();
            foreach ($payments as $paymentCode => $paymentModel) {
                $methods[] = $paymentCode;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->create()->createLog(__METHOD__, $ex->getMessage(), self::I95EXC, 'critical');
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
        return $methods;
    }

    /**
     * Prepare payment data to add in order post data
     *
     * @author Divya Koona
     * @return array
     */
    public function setPaymentInformation()
    {
        $payment = $this->paymentData[0];
        $paymentData = [];
        $paymentData['method'] = isset($payment[self::PAYMENTMETHOD]) ? $payment[self::PAYMENTMETHOD] : '';
        $paymentData['po_number'] = isset($payment['poNumber']) ? $payment['poNumber'] : '';
        $paymentData['cc_type'] = isset($payment['ccType']) ? $payment['ccType'] : '';
        $paymentData['cc_number'] = isset($payment['ccNumber']) ? $payment['ccNumber'] : '';
        $paymentData['cc_exp_month'] = isset($payment['ccExpMonth']) ? $payment['ccExpMonth'] : '';
        $paymentData['cc_exp_year'] = isset($payment['ccExpYear']) ? $payment['ccExpYear'] : '';
        $paymentData['cc_cid'] = isset($payment['ccCid']) ? $payment['ccCid'] : '';
        $chkNumber = $this->dataHelper->getValueFromArray("checkNumber", $payment);
        if ($chkNumber) {
            $additional_data = ['additional_information' => [$chkNumber]];
            $paymentData = (array_merge($paymentData, $additional_data));
        }
        return $paymentData;
    }
}
