<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Plugin;

/**
 * Plugin class to access customer price level through out the context
 */
class PriceLevelContextPlugin
{

    const PRICELEVEL_CONTEXT = "customer_pricelevel";
    /**
     * @var \Magento\Customer\Model\Session
     */
    public $customerSession;

    /**
     * Class constructor to include all the dependencies
     *
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerSession = $customerSession;
    }

    /**
     * \Magento\Framework\App\Http\Context::getVaryString is used by Magento to retrieve unique identifier
     * for selected context,so this is a best place to declare custom context variables
     *
     * @param \Magento\Framework\App\Http\Context $subject
     */
    public function beforeGetVaryString(\Magento\Framework\App\Http\Context $subject)
    {
        $pricelevel = $this->customerSession->getCustomer()->getPricelevel();
        if ($pricelevel != '') {
            $subject->setValue(self::PRICELEVEL_CONTEXT, $pricelevel, '');
        }
    }
}
