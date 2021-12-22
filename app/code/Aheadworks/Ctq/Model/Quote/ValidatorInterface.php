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
namespace Aheadworks\Ctq\Model\Quote;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Quote;

/**
 * Interface ValidatorInterface
 * @package Aheadworks\Ctq\Model\Quote
 */
interface ValidatorInterface
{
    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * @param Quote|QuoteInterface $quote
     * @return bool
     */
    public function isValid($quote);
}
