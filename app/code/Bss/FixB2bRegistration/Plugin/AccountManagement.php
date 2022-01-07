<?php
namespace Bss\FixB2bRegistration\Plugin;

use Magento\Customer\Api\Data\CustomerInterface;

class AccountManagement
{
    protected $helper;

    protected $request;
    public function __construct(
        \Bss\B2bRegistration\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->helper = $helper;
        $this->request = $request;
    }
    public function beforeCreateAccountWithPasswordHash($subject, CustomerInterface $customer, $hash, $redirectUrl = '')
    {
        if ($this->helper->isEnable() && $this->request->getModuleName() == 'btwob') {
            $groupConfig = $this->helper->getCustomerGroup();
            if ($customer->getGroupId() == null) {
                $customer->setGroupId($groupConfig);
            }
        }
        return [$customer, $hash, $redirectUrl = ''];
    }
}
