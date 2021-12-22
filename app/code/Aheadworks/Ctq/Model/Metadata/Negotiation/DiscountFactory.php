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
namespace Aheadworks\Ctq\Model\Metadata\Negotiation;

use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Api\Data\CartInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;

/**
 * Class DiscountFactory
 *
 * @package Aheadworks\Ctq\Model\Metadata\Negotiation
 */
class DiscountFactory
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Create negotiated discount object
     *
     * @param CartInterface $cart
     * @return NegotiatedDiscountInterface
     */
    public function create($cart)
    {
        $data = $this->prepareData($cart);
        return $this->objectManager->create(NegotiatedDiscountInterface::class, ['data' => $data]);
    }

    /**
     * Prepare data
     *
     * @param CartInterface $cart
     * @return array
     */
    private function prepareData($cart)
    {
        /** @var QuoteInterface $quote */
        $quote = $cart->getExtensionAttributes()->getAwCtqQuote();

        return [
            NegotiatedDiscountInterface::DISCOUNT_TYPE => $quote->getNegotiatedDiscountType(),
            NegotiatedDiscountInterface::DISCOUNT_VALUE => $quote->getNegotiatedDiscountValue(),
        ];
    }
}
