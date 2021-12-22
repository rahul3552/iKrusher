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
namespace Aheadworks\Ctq\Model\Source\Quote;

/**
 * Class Action
 * @package Aheadworks\Ctq\Model\Source\Quote
 */
class Action
{
    /**#@+
     * Constants defined for quote actions
     */
    const APPROVE = 'approve';
    const SAVE = 'save';
    const SELL = 'sell';
    const SUBMIT_FOR_APPROVAL = 'submit_for_approval';
    const BUY = 'buy';
    const SORT = 'sort';
    const DECLINE = 'decline';
    const REQUOTE = 'requote';
    /**#@-*/
}
