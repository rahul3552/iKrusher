<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_BillPay
 */

namespace I95DevConnect\BillPay\Model;

use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Payment\Model\Config;

/**
 * Model class for Payment method as options
 */
class PaymentOptions extends \Magento\Framework\DataObject implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var ScopeConfigInterface
     */
    protected $_appConfigScopeConfigInterface;

    /**
     * @var Config
     */
    protected $_paymentModelConfig;

    /**
     * @param ScopeConfigInterface $appConfigScopeConfigInterface
     * @param Config               $paymentModelConfig
     */
    public function __construct(
        ScopeConfigInterface $appConfigScopeConfigInterface,
        Config $paymentModelConfig
    ) {
        $this->_appConfigScopeConfigInterface = $appConfigScopeConfigInterface;
        $this->_paymentModelConfig = $paymentModelConfig;
    }

    public function toOptionArray()
    {
        $paymentmethods = [
            1 => 'chargelogic_connect',
            2 => 'payflowpro',
            3 => 'authnetcim'
        ];
        $payments = $this->_paymentModelConfig->getActiveMethods();
        $methods = [];
        foreach ($payments as $paymentCode => $paymentModel) {
            if (in_array($paymentCode, $paymentmethods)) {
                $paymentTitle = $this->_appConfigScopeConfigInterface
                    ->getValue('payment/' . $paymentCode . '/title');
                $methods[$paymentCode] = [
                    'label' => $paymentTitle,
                    'value' => $paymentCode
                ];
            }
        }
        return $methods;
    }
}
