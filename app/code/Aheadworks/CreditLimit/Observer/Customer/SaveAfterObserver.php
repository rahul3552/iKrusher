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

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Aheadworks\CreditLimit\Model\Customer\CreditLimit\DataProvider as CreditLimitDataProvider;
use Aheadworks\CreditLimit\Model\Customer\Backend\BalanceUpdater;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface as ParamsInterface;

/**
 * Class SaveAfterObserver
 *
 * @package Aheadworks\CreditLimit\Observer\Customer
 */
class SaveAfterObserver implements ObserverInterface
{
    /**
     * @var BalanceUpdater
     */
    private $balanceUpdater;

    /**
     * @param BalanceUpdater $balanceUpdater
     */
    public function __construct(
        BalanceUpdater $balanceUpdater
    ) {
        $this->balanceUpdater = $balanceUpdater;
    }

    /**
     * Customer credit limit update after save
     *
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /* @var $request RequestInterface */
        $request = $observer->getRequest();
        $creditLimitData = $request->getParam(CreditLimitDataProvider::CREDIT_LIMIT_DATA_SCOPE);
        $customerId = $request->getParam(ParamsInterface::CUSTOMER_ID);
        $defaultData = $request->getParam('use_default');

        $this->balanceUpdater->updateCreditLimit($customerId, $creditLimitData, $defaultData);
        $this->balanceUpdater->updateCreditBalance($customerId, $creditLimitData);
    }
}
