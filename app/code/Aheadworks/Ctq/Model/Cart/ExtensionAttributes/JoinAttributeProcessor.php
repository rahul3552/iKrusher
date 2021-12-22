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
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Model\Cart\ExtensionAttributes;

use Aheadworks\Ctq\Api\QuoteRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartExtensionFactory;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Class JoinAttributeProcessor
 * @package Aheadworks\Ctq\Model\Cart\ExtensionAttributes
 */
class JoinAttributeProcessor
{
    /**
     * @var CartExtensionFactory
     */
    private $cartExtensionFactory;

    /**
     * @var QuoteRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @param CartExtensionFactory $cartExtensionFactory
     * @param QuoteRepositoryInterface $quoteRepository
     */
    public function __construct(
        CartExtensionFactory $cartExtensionFactory,
        QuoteRepositoryInterface $quoteRepository
    ) {
        $this->cartExtensionFactory = $cartExtensionFactory;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Attach quote data to cart object
     *
     * @param CartInterface $cart
     * @return CartInterface
     */
    public function process($cart)
    {
        try {
            $quote = $this->quoteRepository->getByCartId($cart->getId());

            $extensionAttributes = $cart->getExtensionAttributes()
                ? $cart->getExtensionAttributes()
                : $this->cartExtensionFactory->create();
            $extensionAttributes->setAwCtqQuote($quote);
            $cart->setExtensionAttributes($extensionAttributes);
        } catch (NoSuchEntityException $e) {
        }

        return $cart;
    }
}
