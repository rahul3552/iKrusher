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
namespace Aheadworks\Ctq\Block\Account;

use Aheadworks\Ctq\Model\Quote\Permission\Checker as PermissionChecker;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\DefaultPathInterface;
use Magento\Framework\View\Element\Html\Link\Current as LinkCurrent;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Link
 *
 * @package Aheadworks\Ctq\Block\Account
 */
class Link extends LinkCurrent
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var PermissionChecker
     */
    private $permissionChecker;

    /**
     * @param Context $context
     * @param DefaultPathInterface $defaultPath
     * @param CustomerSession $customerSession
     * @param PermissionChecker $permissionChecker
     * @param array $data
     */
    public function __construct(
        Context $context,
        DefaultPathInterface $defaultPath,
        CustomerSession $customerSession,
        PermissionChecker $permissionChecker,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->customerSession = $customerSession;
        $this->permissionChecker = $permissionChecker;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _toHtml()
    {
        $customerId = $this->customerSession->getCustomer()->getId();
        $storeId = $this->_storeManager->getStore()->getId();
        if (!$this->permissionChecker->check($customerId, $storeId)) {
            return '';
        }

        return parent::_toHtml();
    }
}
