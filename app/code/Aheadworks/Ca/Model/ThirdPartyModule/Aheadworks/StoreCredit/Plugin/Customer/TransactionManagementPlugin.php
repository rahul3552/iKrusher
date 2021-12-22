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
 * @package    Ca
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Plugin\Customer;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Model\SummaryManagement;

/**
 * Class TransactionManagementPlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\StoreCredit\Plugin\Customer
 */
class TransactionManagementPlugin
{
    /**
     * @var SummaryManagement
     */
    private $summaryManagement;

    /**
     * @param SummaryManagement $summaryManagement
     */
    public function __construct(
        SummaryManagement $summaryManagement
    ) {
        $this->summaryManagement = $summaryManagement;
    }

    /**
     * Adjust transaction balance for company user customer
     *
     * @param \Aheadworks\StoreCredit\Api\TransactionManagementInterface $subject
     * @param int $transactionId
     * @param float $balance
     * @return array
     */
    public function beforeUpdateCurrentBalance($subject, $transactionId, $balance)
    {
        $balance = $this->summaryManagement->getCurrentCustomerBalanceByTransactionId($transactionId, $balance);
        return [$transactionId, $balance];
    }
}
