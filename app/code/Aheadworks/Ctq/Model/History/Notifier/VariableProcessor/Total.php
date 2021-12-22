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
namespace Aheadworks\Ctq\Model\History\Notifier\VariableProcessor;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Email\VariableProcessorInterface;
use Aheadworks\Ctq\Model\Source\History\EmailVariables;
use Aheadworks\Ctq\ViewModel\Customer\Quote;

/**
 * Class Total
 * @package Aheadworks\Ctq\Model\History\Notifier\VariableProcessor
 */
class Total implements VariableProcessorInterface
{
    /**
     * @var Quote
     */
    private $quoteViewModel;

    /**
     * @param Quote $quoteViewModel
     */
    public function __construct(Quote $quoteViewModel)
    {
        $this->quoteViewModel = $quoteViewModel;
    }

    /**
     * @inheritdoc
     */
    public function prepareVariables($variables)
    {
        /** @var QuoteInterface $quote */
        $quote = $variables[EmailVariables::QUOTE];
        $variables[EmailVariables::TOTAL] = $this->quoteViewModel->getQuoteTotalFormatted($quote->getBaseQuoteTotal());

        return $variables;
    }
}
