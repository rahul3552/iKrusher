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
namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\Totals;

use Magento\Sales\Block\Adminhtml\Order\Create\Totals\DefaultTotals as SalesDefaultTotals;

/**
 * Class DefaultTotals
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\Quote\Edit\Totals
 */
class DefaultTotals extends SalesDefaultTotals
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'Aheadworks_Ctq::quote/edit/totals/default.phtml';
}
