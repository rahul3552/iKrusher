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

use Aheadworks\OneStepCheckout\Api\CheckoutSectionsManagementInterface;
use Aheadworks\OneStepCheckout\Api\GuestCheckoutSectionsManagementInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;

/**
 * Class GuestCheckoutSectionsManagement
 * @package Aheadworks\OneStepCheckout\Model
 */
class GuestCheckoutSectionsManagement implements GuestCheckoutSectionsManagementInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var CheckoutSectionsManagementInterface
     */
    private $sectionManagement;

    /**
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CheckoutSectionsManagementInterface $sectionManagement
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CheckoutSectionsManagementInterface $sectionManagement
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->sectionManagement = $sectionManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionsDetails(
        $cartId,
        $sections,
        AddressInterface $shippingAddress = null,
        AddressInterface $billingAddress = null
    ) {
        $quoteIdMask = $this->quoteIdMaskFactory->create()
            ->load($cartId, 'masked_id');
        return $this->sectionManagement->getSectionsDetails(
            $quoteIdMask->getQuoteId(),
            $sections,
            $shippingAddress,
            $billingAddress
        );
    }
}
