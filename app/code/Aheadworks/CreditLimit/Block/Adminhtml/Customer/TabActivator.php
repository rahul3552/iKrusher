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
 * @package    CreditLimit
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Block\Adminhtml\Customer;

use Magento\Backend\Block\Template;

/**
 * Class TabActivator
 *
 * @method string|null getParamToTrigger()
 * @method string|null getParamValue()
 * @method string|null getTabId()
 * @package Aheadworks\CreditLimit\Block\Adminhtml\Customer
 */
class TabActivator extends Template
{
    /**
     * @inheritdoc
     */
    protected $_template = 'Aheadworks_CreditLimit::customer/tab-activator.phtml';

    /**
     * Check is active
     *
     * @return bool
     */
    public function isActive()
    {
        $param = $this->_request->getParam($this->getParamToTrigger());
        if ($param == $this->getParamValue()) {
            return true;
        }

        return false;
    }
}
