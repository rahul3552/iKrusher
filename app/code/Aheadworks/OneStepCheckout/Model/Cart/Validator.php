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
namespace Aheadworks\OneStepCheckout\Model\Cart;

use Magento\Framework\Validator\AbstractValidator;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Validator\MinimumOrderAmount\ValidationMessage;

/**
 * Class Validator
 * @package Aheadworks\OneStepCheckout\Model\Cart
 */
class Validator extends AbstractValidator
{
    /**
     * @var ValidationMessage
     */
    private $validationMessage;

    /**
     * @param ValidationMessage $validationMessage
     */
    public function __construct(ValidationMessage $validationMessage)
    {
        $this->validationMessage = $validationMessage;
    }

    /**
     * Returns true if and only if quote entity meets the validation requirements
     *
     * @param Quote $quote
     * @return bool
     */
    public function isValid($quote)
    {
        $isValid = true;
        $this->_clearMessages();

        if (!$quote->validateMinimumAmount()) {
            $isValid = false;
        }
        if ($quote->getHasError()) {
            $isValid = false;
            foreach ($quote->getErrors() as $error) {
                $this->_addMessages([$error->getText()]);
            }
        }

        return $isValid;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessages()
    {
        return array_merge($this->_messages, [$this->validationMessage->getMessage()]);
    }
}
