<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Plugin\Order\Create;

use Magento\Framework\View\Element\Template;

/**
 * Child template based on alias
 */
class Data extends Template
{
    /**
     * Set child template if the alias is form_account before getChildHtml.
     *
     * @param string $subject
     * @param string $alias
     * @param boolean $useCache
     * @codingStandardsIgnoreStart
     */
    public function beforegetChildHtml($subject, $alias = 'form_account', $useCache = true)
    {
        if ($alias == 'form_account') {
            echo $this->getLayout()
                ->createBlock('\I95DevConnect\NetTerms\Block\Adminhtml\Order\Create\NetTerms')
                ->setTemplate('I95DevConnect_NetTerms::order/create/form/account.phtml')
                ->toHtml();
        }
    }
    // @codingStandardsIgnoreEnd
}
