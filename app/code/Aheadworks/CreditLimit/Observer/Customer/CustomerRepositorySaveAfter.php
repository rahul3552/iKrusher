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
namespace Aheadworks\CreditLimit\Observer\Customer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Customer\Api\Data\CustomerInterface;
use Aheadworks\CreditLimit\Model\Customer\CreditLimit\UpdateManagement;

/**
 * Class CustomerRepositorySaveAfter
 *
 * @package Aheadworks\CreditLimit\Observer\Customer
 */
class CustomerRepositorySaveAfter implements ObserverInterface
{
    /**
     * @var UpdateManagement
     */
    private $updateManagement;

    /**
     * @param UpdateManagement $updateManagement
     */
    public function __construct(
        UpdateManagement $updateManagement
    ) {
        $this->updateManagement = $updateManagement;
    }

    /**
     * Check customer group after customer saving
     *
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /* @var $newCustomer CustomerInterface */
        $newCustomer = $observer->getCustomerDataObject();
        /* @var $oldCustomer CustomerInterface */
        $oldCustomer = $observer->getOrigCustomerDataObject();
        $oldCustomerGroup = $oldCustomer ? $oldCustomer->getGroupId() : 0;
        if ($oldCustomerGroup != $newCustomer->getGroupId()) {
            $this->updateManagement->updateCreditLimitOnGroupChange(
                $oldCustomerGroup,
                $newCustomer
            );
        }
    }
}
