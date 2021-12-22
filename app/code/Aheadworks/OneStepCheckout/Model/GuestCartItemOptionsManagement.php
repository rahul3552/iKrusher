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

use Aheadworks\OneStepCheckout\Api\CartItemOptionsManagementInterface;
use Aheadworks\OneStepCheckout\Api\GuestCartItemOptionsManagementInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;

/**
 * Class GuestCartItemOptionsManagement
 * @package Aheadworks\OneStepCheckout\Model
 */
class GuestCartItemOptionsManagement implements GuestCartItemOptionsManagementInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var CartItemOptionsManagementInterface
     */
    private $cartItemOptionsManagement;

    /**
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CartItemOptionsManagementInterface $cartItemOptionsManagement
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CartItemOptionsManagementInterface $cartItemOptionsManagement
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->cartItemOptionsManagement = $cartItemOptionsManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function update($itemId, $cartId, $options)
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()
            ->load($cartId, 'masked_id');
        return $this->cartItemOptionsManagement->update(
            $itemId,
            $quoteIdMask->getQuoteId(),
            $options
        );
    }
}
