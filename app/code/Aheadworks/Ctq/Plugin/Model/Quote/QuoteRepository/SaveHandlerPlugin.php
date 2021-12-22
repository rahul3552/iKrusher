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
namespace Aheadworks\Ctq\Plugin\Model\Quote\QuoteRepository;

use Aheadworks\Ctq\Model\Cart\Purchase\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\QuoteRepository\SaveHandler;

/**
 * Class SaveHandlerPlugin
 * @package Aheadworks\Ctq\Plugin\Model\Quote\QuoteRepository
 */
class SaveHandlerPlugin
{
    /**
     * @var Validator
     */
    private $cartValidator;

    /**
     * @param Validator $cartValidator
     */
    public function __construct(
        Validator $cartValidator
    ) {
        $this->cartValidator = $cartValidator;
    }

    /**
     * Validate cart before save
     *
     * @param SaveHandler $subject
     * @param CartInterface $quote
     * @return void
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave($subject, CartInterface $quote)
    {
        if (!$this->cartValidator->isValid($quote) || $quote->getAwCtqThrowException()) {
            throw new LocalizedException(__('We can\'t update your shopping cart right now. Deal was settled.'));
        }
    }
}
