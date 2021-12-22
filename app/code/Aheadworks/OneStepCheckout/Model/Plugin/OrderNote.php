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

use Aheadworks\OneStepCheckout\Model\OrderNote\PaymentDataExtensionProcessor;
use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\Quote\Api\Data\PaymentInterface;

/**
 * Class OrderNote
 * @package Aheadworks\OneStepCheckout\Model\Plugin
 */
class OrderNote
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
     * @param int $cartId
     * @param PaymentInterface $paymentMethod
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSavePaymentInformationAndPlaceOrder(
        PaymentInformationManagementInterface $subject,
        $cartId,
        PaymentInterface $paymentMethod
    ) {
        $this->paymentDataProcessor->process($paymentMethod, $cartId);
    }

    /**
     * @param PaymentInformationManagementInterface $subject
     * @param int $cartId
     * @param PaymentInterface $paymentMethod
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSavePaymentInformation(
        PaymentInformationManagementInterface $subject,
        $cartId,
        PaymentInterface $paymentMethod
    ) {
        $this->paymentDataProcessor->process($paymentMethod, $cartId);
    }
}
