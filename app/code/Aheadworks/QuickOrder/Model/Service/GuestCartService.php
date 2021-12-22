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
 * @package    QuickOrder
 * @version    1.0.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Model\Service;

use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Aheadworks\QuickOrder\Api\CartManagementInterface;
use Aheadworks\QuickOrder\Api\GuestCartManagementInterface;

/**
 * Class GuestCartService
 *
 * @package Aheadworks\QuickOrder\Model\Service
 */
class GuestCartService implements GuestCartManagementInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var CartManagementInterface
     */
    private $cartManagement;

    /**
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CartManagementInterface $cartManagement
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CartManagementInterface $cartManagement
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->cartManagement = $cartManagement;
    }

    /**
     * @inheritdoc
     */
    public function addListToCart($listId, $cartId)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->cartManagement->addListToCart($listId, $quoteIdMask->getQuoteId());
    }
}
