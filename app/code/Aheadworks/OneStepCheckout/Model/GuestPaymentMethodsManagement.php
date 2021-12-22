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
namespace Aheadworks\OneStepCheckout\Model;

use Aheadworks\OneStepCheckout\Api\PaymentMethodsManagementInterface;
use Aheadworks\OneStepCheckout\Api\GuestPaymentMethodsManagementInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;

/**
 * Class GuestPaymentMethodsManagement
 * @package Aheadworks\OneStepCheckout\Model
 */
class GuestPaymentMethodsManagement implements GuestPaymentMethodsManagementInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var PaymentMethodsManagementInterface
     */
    private $paymentMethodsManagement;

    /**
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param PaymentMethodsManagementInterface $paymentMethodsManagement
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        PaymentMethodsManagementInterface $paymentMethodsManagement
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->paymentMethodsManagement = $paymentMethodsManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentMethods(
        $cartId,
        AddressInterface $shippingAddress,
        AddressInterface $billingAddress = null
    ) {
        $quoteIdMask = $this->quoteIdMaskFactory->create()
            ->load($cartId, 'masked_id');
        return $this->paymentMethodsManagement->getPaymentMethods(
            $quoteIdMask->getQuoteId(),
            $shippingAddress,
            $billingAddress
        );
    }
}
