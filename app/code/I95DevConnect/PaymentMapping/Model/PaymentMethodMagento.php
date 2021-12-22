<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PaymentMapping
 */

namespace I95DevConnect\PaymentMapping\Model;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Model\Config;

/**
 * payment mapping management class
 */
class PaymentMethodMagento
{
    /**
     * @var Config
     */
    public $paymentConfig;

    /**
     * payment mapping management constructor
     *
     * @param Config $paymentConfig
     */
    public function __construct(
        Config $paymentConfig
    ) {

        $this->paymentConfig = $paymentConfig;
    }

    /**
     * get available payment method
     * @return array
     * @throws LocalizedException
     */
    public function availablePaymentMethods()
    {
        try {
            $codeList = [];
            $activePaymentMethods = $this->paymentConfig->getActiveMethods();
            foreach ($activePaymentMethods as $paymentMethod => $paymentModel) {
                $codeList[] = $paymentMethod;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            throw new LocalizedException(__($e->getMessage()));
        }

        return $codeList;
    }
}
