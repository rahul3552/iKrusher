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
namespace Aheadworks\Ctq\ViewModel\Customer\Export;

use Aheadworks\Ctq\ViewModel\Customer\Quote as CustomerQuote;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class Quote
 * @package Aheadworks\Ctq\ViewModel\Customer\Export
 */
class Quote extends CustomerQuote implements ArgumentInterface
{
    /**
     * @inheritDoc
     */
    public function isAllowSorting($quote)
    {
        return false;
    }
}
