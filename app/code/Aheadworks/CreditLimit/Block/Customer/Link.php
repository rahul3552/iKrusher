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
namespace Aheadworks\CreditLimit\Block\Customer;

use Aheadworks\CreditLimit\Api\CustomerManagementInterface;
use Magento\Framework\View\Element\Html\Link\Current as LinkCurrent;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\DefaultPathInterface;

/**
 * Class Link
 *
 * @package Aheadworks\CreditLimit\Block\Customer
 */
class Link extends LinkCurrent
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var CustomerManagementInterface
     */
    private $customerManagement;

    /**
     * @param Context $context
     * @param DefaultPathInterface $defaultPath
     * @param CustomerSession $customerSession
     * @param CustomerManagementInterface $customerManagement
     * @param array $data
     */
    public function __construct(
        Context $context,
        DefaultPathInterface $defaultPath,
        CustomerSession $customerSession,
        CustomerManagementInterface $customerManagement,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->customerSession = $customerSession;
        $this->customerManagement = $customerManagement;
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        $customerId = $this->customerSession->getCustomerId();
        if (!$this->customerManagement->isCreditLimitAvailable($customerId)) {
            return '';
        }

        return parent::_toHtml();
    }
}
