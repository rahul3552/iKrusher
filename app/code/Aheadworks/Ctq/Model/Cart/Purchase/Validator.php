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
namespace Aheadworks\Ctq\Model\Cart\Purchase;

use Magento\Framework\Validator\AbstractValidator;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;

/**
 * Class Validator
 * @package Aheadworks\Ctq\Model\Cart\Purchase
 */
class Validator extends AbstractValidator
{
    /**
     * @var AbstractValidator[]
     */
    private $validators;

    /**
     * @param AbstractValidator[] $validators
     */
    public function __construct(array $validators = [])
    {
        $this->validators = $validators;
    }

    /**
     * Validate cart
     *
     * @param CartInterface|Quote $cart
     * @return bool
     */
    public function isValid($cart)
    {
        if ($cart->getExtensionAttributes()
            && $cart->getExtensionAttributes()->getAwCtqQuote()
            && (!$cart instanceof Quote
                || ($cart instanceof Quote && !$cart->getAwCtqIsNotRequireValidation())
            )
        ) {
            foreach ($this->validators as $validator) {
                if (!$validator->isValid($cart)) {
                    $this->_addMessages($validator->getMessages());
                }
            }
        }

        return empty($this->getMessages());
    }
}
