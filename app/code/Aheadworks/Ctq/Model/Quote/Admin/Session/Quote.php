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
namespace Aheadworks\Ctq\Model\Quote\Admin\Session;

use Magento\Backend\Model\Session\Quote as QuoteSession;
use Magento\Quote\Model\Quote as QuoteModel;

/**
 * Class Quote
 *
 * @package Aheadworks\Ctq\Model\Quote\Admin\Session
 */
class Quote extends QuoteSession
{
    /**
     * Set quote object to session
     *
     * @param QuoteModel $quote
     * @return $this
     */
    public function setQuote($quote)
    {
        $this->_quote = $quote;
        return $this;
    }
}
