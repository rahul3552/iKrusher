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

use Aheadworks\OneStepCheckout\Api\DataFieldCompletenessLoggerInterface;
use Aheadworks\OneStepCheckout\Api\GuestDataFieldCompletenessLoggerInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;

/**
 * Class GuestDataFieldCompletenessLogger
 * @package Aheadworks\OneStepCheckout\Model
 */
class GuestDataFieldCompletenessLogger implements GuestDataFieldCompletenessLoggerInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var DataFieldCompletenessLoggerInterface
     */
    private $completenessLogger;

    /**
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param DataFieldCompletenessLoggerInterface $completenessLogger
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        DataFieldCompletenessLoggerInterface $completenessLogger
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->completenessLogger = $completenessLogger;
    }

    /**
     * {@inheritdoc}
     */
    public function log($cartId, array $fieldCompleteness)
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()
            ->load($cartId, 'masked_id');
        $this->completenessLogger->log($quoteIdMask->getQuoteId(), $fieldCompleteness);
    }
}
