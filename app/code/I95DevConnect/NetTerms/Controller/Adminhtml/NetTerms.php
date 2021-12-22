<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Abstract class for netterms
 */
abstract class NetTerms extends Action
{
    /**
     * Check admin permissions for this controller
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('I95DevConnect_NetTerms::netterms');
    }
}
