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

use Magento\Framework\Validator\AbstractValidator;
use Magento\Quote\Api\Data\CartInterface;
use Aheadworks\Ctq\Api\Data\QuoteInterface;

/**
 * Class Validator
 *
 * @package Aheadworks\Ctq\Model\Quote\Discount
 */
class Validator extends AbstractValidator
{
    /**
     * Returns true if cart meets the validation requirements
     *
     * @param CartInterface $cart
     * @return bool
     */
    public function isValid($cart)
    {
        $this->_clearMessages();

        if ($cart->getExtensionAttributes()
            && $cart->getExtensionAttributes()->getAwCtqQuote()
        ) {
            /** @var QuoteInterface $quote */
            $quote = $cart->getExtensionAttributes()->getAwCtqQuote();
            if (!$quote->getNegotiatedDiscountType()) {
                $this->_addMessages(['Can\'t apply negotiated discount. No discount type is specified.']);
            }
            if (!$quote->getNegotiatedDiscountValue()) {
                $this->_addMessages(['Can\'t apply negotiated discount. No discount value is specified.']);
            }
        } else {
            $this->_addMessages(['Cart does\'nt contain quote.']);
        }
        return empty($this->getMessages());
    }
}
