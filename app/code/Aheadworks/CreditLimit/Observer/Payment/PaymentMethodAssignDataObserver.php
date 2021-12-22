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
namespace Aheadworks\CreditLimit\Observer\Payment;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;

/**
 * Class PaymentMethodAssignDataObserver
 *
 * @package Aheadworks\CreditLimit\Observer\Payment
 */
class PaymentMethodAssignDataObserver implements ObserverInterface
{
    /**
     * Assign payment method data
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $poNumber = $observer->getData(AbstractDataAssignObserver::DATA_CODE)->getPoNumber();
        if ($poNumber) {
            $observer->getPaymentModel()->setPoNumber($poNumber);
        }
    }
}
