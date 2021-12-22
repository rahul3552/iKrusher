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
namespace Aheadworks\OneStepCheckout\Api;

/**
 * Interface GuestPaymentMethodsManagementInterface
 * @package Aheadworks\OneStepCheckout\Api
 * @api
 */
interface GuestPaymentMethodsManagementInterface
{
    /**
     * Get list of available payment methods list
     *
     * @param string $cartId
     * @param \Magento\Quote\Api\Data\AddressInterface $shippingAddress
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @return \Magento\Quote\Api\Data\PaymentMethodInterface[]
     * @throws \Magento\Framework\Exception\InputException
     */
    public function getPaymentMethods(
        $cartId,
        \Magento\Quote\Api\Data\AddressInterface $shippingAddress,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    );
}
