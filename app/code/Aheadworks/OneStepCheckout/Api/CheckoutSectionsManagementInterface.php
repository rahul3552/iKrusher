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
 * Interface CheckoutSectionsManagementInterface
 * @package Aheadworks\OneStepCheckout\Api
 * @api
 */
interface CheckoutSectionsManagementInterface
{
    /**
     * Get sections details
     *
     * @param int $cartId
     * @param \Aheadworks\OneStepCheckout\Api\Data\CheckoutSectionInformationInterface[] $sections
     * @param \Magento\Quote\Api\Data\AddressInterface|null $shippingAddress
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @param int|null $negotiableQuoteId
     * @return \Aheadworks\OneStepCheckout\Api\Data\CheckoutSectionsDetailsInterface
     * @throws \Magento\Framework\Exception\InputException
     */
    public function getSectionsDetails(
        $cartId,
        $sections,
        \Magento\Quote\Api\Data\AddressInterface $shippingAddress = null,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null,
        $negotiableQuoteId = null
    );
}
