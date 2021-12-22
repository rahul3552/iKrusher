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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Plugin\Block;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Model\CreditLimitManagement;

/**
 * Class TotalListPlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\CreditLimit\Plugin\Block
 */
class TotalListPlugin
{
    /**
     * @var CreditLimitManagement
     */
    private $creditLimitManagement;

    /**
     * @param CreditLimitManagement $creditLimitManagement
     */
    public function __construct(
        CreditLimitManagement $creditLimitManagement
    ) {
        $this->creditLimitManagement = $creditLimitManagement;
    }

    /**
     * Change customer to company admin if required
     *
     * @param \Aheadworks\CreditLimit\Block\Customer\TotalList $subject
     * @param int $customerId
     * @return int
     */
    public function afterGetCustomerId($subject, $customerId)
    {
        $customerId = $this->creditLimitManagement->changeCustomerIdIfNeeded($customerId);
        return $customerId;
    }
}
