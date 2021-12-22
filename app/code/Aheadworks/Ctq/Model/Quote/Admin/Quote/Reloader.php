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
namespace Aheadworks\Ctq\Model\Quote\Admin\Quote;

use Aheadworks\Ctq\Model\Quote\Admin\Session\Quote as QuoteSession;

/**
 * Class Reloader
 *
 * @package Aheadworks\Ctq\Model\Quote\Admin\Quote
 */
class Reloader
{
    /**
     * @var QuoteSession
     */
    private $quoteSession;

    /**
     * @param QuoteSession $quoteSession
     */
    public function __construct(
        QuoteSession $quoteSession
    ) {
        $this->quoteSession = $quoteSession;
    }

    /**
     * Reload a quote stored in session
     */
    public function reload()
    {
        $quote = $this->quoteSession->getQuote();
        if ($quote) {
            $quote->load($quote->getId());
        }
    }
}
