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
namespace Aheadworks\Ctq\Model\Quote\Discount;

use Aheadworks\Ctq\Model\Quote\Discount\Calculator\Pool as CalculatorPool;
use Aheadworks\Ctq\Model\Metadata\Quote\DiscountFactory as QuoteDiscountFactory;
use Aheadworks\Ctq\Model\Metadata\Quote\Discount;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartInterface;
use Aheadworks\Ctq\Model\Metadata\Negotiation\DiscountFactory as NegotiatedDiscountFactory;

/**
 * Class Calculator
 *
 * @package Aheadworks\Ctq\Model\Quote\Discount
 */
class Calculator
{
    /**
     * @var QuoteDiscountFactory
     */
    private $quoteDiscountFactory;

    /**
     * @var NegotiatedDiscountFactory
     */
    private $negotiatedDiscountFactory;

    /**
     * @var CalculatorPool
     */
    private $calculatorPool;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @param QuoteDiscountFactory $quoteDiscountFactory
     * @param NegotiatedDiscountFactory $negotiatedDiscountFactory
     * @param CalculatorPool $calculatorPool
     * @param Validator $validator
     */
    public function __construct(
        QuoteDiscountFactory $quoteDiscountFactory,
        NegotiatedDiscountFactory $negotiatedDiscountFactory,
        CalculatorPool $calculatorPool,
        Validator $validator
    ) {
        $this->quoteDiscountFactory = $quoteDiscountFactory;
        $this->negotiatedDiscountFactory = $negotiatedDiscountFactory;
        $this->calculatorPool = $calculatorPool;
        $this->validator = $validator;
    }

    /**
     * Calculate discount
     *
     * @param CartItemInterface[]|AbstractItem[] $items
     * @param AddressInterface $address
     * @param CartInterface $cart
     * @return Discount
     */
    public function calculate($items, $address, $cart)
    {
        if ($this->validator->isValid($cart) && is_array($items) && !empty($items)) {
            $negotiatedDiscount = $this->negotiatedDiscountFactory->create($cart);
            $calculator = $this->calculatorPool->getCalculatorByType($negotiatedDiscount->getDiscountType());
            $quoteDiscount = $calculator->calculate($items, $address, $negotiatedDiscount);
        } else {
            $quoteDiscount = $this->quoteDiscountFactory->create();
        }

        return $quoteDiscount;
    }
}
