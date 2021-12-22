<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Model\ConfigProvider;

use Magento\Quote\Api\PaymentMethodManagementInterface;

/**
 * Class PaymentMethodList
 * @package Aheadworks\OneStepCheckout\Model\ConfigProvider
 */
class PaymentMethodList
{
    /**
     * @var PaymentMethodManagementInterface
     */
    private $paymentMethodManagement;

    /**
     * @param PaymentMethodManagementInterface $paymentMethodManagement
     */
    public function __construct(PaymentMethodManagementInterface $paymentMethodManagement)
    {
        $this->paymentMethodManagement = $paymentMethodManagement;
    }

    /**
     * Get payment methods config data
     *
     * @param int $cartId
     * @return array
     */
    public function getPaymentMethods($cartId)
    {
        $result = [];
        foreach ($this->paymentMethodManagement->getList($cartId) as $method) {
            $result[] = [
                'code' => $method->getCode(),
                'title' => $method->getTitle()
            ];
        }
        return $result;
    }
}
