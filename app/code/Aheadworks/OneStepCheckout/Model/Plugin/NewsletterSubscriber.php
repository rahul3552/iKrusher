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
namespace Aheadworks\OneStepCheckout\Model\Plugin;

use Aheadworks\OneStepCheckout\Model\Newsletter\PaymentDataExtensionProcessor;
use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Api\Data\AddressInterface;

/**
 * Class NewsletterSubscriber
 * @package Aheadworks\OneStepCheckout\Model\Plugin
 */
class NewsletterSubscriber
{
    /**
     * @var PaymentDataExtensionProcessor
     */
    private $paymentDataProcessor;

    /**
     * @param PaymentDataExtensionProcessor $paymentDataProcessor
     */
    public function __construct(PaymentDataExtensionProcessor $paymentDataProcessor)
    {
        $this->paymentDataProcessor = $paymentDataProcessor;
    }

    /**
     * @param PaymentInformationManagementInterface $subject
     * @param \Closure $proceed
     * @param int $cartId
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface|null $billingAddress
     * @return int Order ID
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSavePaymentInformationAndPlaceOrder(
        PaymentInformationManagementInterface $subject,
        \Closure $proceed,
        $cartId,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        $orderId = $proceed($cartId, $paymentMethod, $billingAddress);
        $this->paymentDataProcessor->process($paymentMethod);

        return $orderId;
    }

    /**
     * @param PaymentInformationManagementInterface $subject
     * @param \Closure $proceed
     * @param int $cartId
     * @param PaymentInterface $paymentMethod
     * @param AddressInterface|null $billingAddress
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSavePaymentInformation(
        PaymentInformationManagementInterface $subject,
        \Closure $proceed,
        $cartId,
        PaymentInterface $paymentMethod,
        AddressInterface $billingAddress = null
    ) {
        $result = $proceed($cartId, $paymentMethod, $billingAddress);
        $this->paymentDataProcessor->process($paymentMethod);

        return $result;
    }
}
