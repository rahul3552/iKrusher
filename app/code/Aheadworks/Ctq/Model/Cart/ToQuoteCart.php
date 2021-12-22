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
namespace Aheadworks\Ctq\Model\Cart;

use Aheadworks\Ctq\Api\Data\QuoteCartInterface;
use Aheadworks\Ctq\Api\Data\QuoteCartInterfaceFactory;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class ToQuoteCart
 * @package Aheadworks\Ctq\Model\Cart
 */
class ToQuoteCart
{
    /**
     * @var QuoteCartInterfaceFactory
     */
    private $quoteCartFactory;

    /**
     * @var Converter
     */
    private $cartConverter;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param QuoteCartInterfaceFactory $quoteCartFactory
     * @param Converter $cartConverter
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        QuoteCartInterfaceFactory $quoteCartFactory,
        Converter $cartConverter,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->quoteCartFactory = $quoteCartFactory;
        $this->cartConverter = $cartConverter;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Convert cart to quote
     *
     * @param CartInterface|Quote $cart
     * @return QuoteCartInterface
     */
    public function convert($cart)
    {
        $cartArray = $this->cartConverter->toArray($cart);
        $quoteCart = $this->quoteCartFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $quoteCart,
            $cartArray,
            QuoteCartInterface::class
        );

        return $quoteCart;
    }
}
