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
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\Page;

use Magento\Backend\Block\Template;

/**
 * Page Menu
 *
 * @method Menu setTitle(string $title)
 * @method string getTitle()
 *
 * @package Aheadworks\OneStepCheckout\Block\Adminhtml\Page
 * @codeCoverageIgnore
 */
class Menu extends Template
{
    /**
     * @inheritdoc
     */
    protected $_template = 'Aheadworks_OneStepCheckout::page/menu.phtml';
}
